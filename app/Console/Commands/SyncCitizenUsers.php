<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\RoleGroup;
use App\Support\IndianMobileNumber;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SyncCitizenUsers extends Command
{
    protected $signature = 'citizens:sync-users
                            {--chunk=5000 : Number of purchaser records per batch}
                            {--from-id=0 : Resume from this PrivatePurchaserId (exclusive)}
                            {--dry-run : Report actions without writing to the database}';

    protected $description = 'Sync every property_private_purchasers row into users (one user per PrivatePurchaserId)';

    private int $processed = 0;

    private int $usersCreated = 0;

    private int $usersLinked = 0;

    private int $usersSkipped = 0;

    private int $rolesCreated = 0;

    private int $rolesSkipped = 0;

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

        $citizenGroup = RoleGroup::where('slug', 'citizen')->first();
        $citizenRole = Role::where('slug', 'citizen')->first();

        if (! $citizenGroup || ! $citizenRole) {
            $this->error('Citizen role/group not found. Run RoleGroupSeeder and RoleSeeder first.');

            return self::FAILURE;
        }

        $chunkSize = max(100, (int) $this->option('chunk'));
        $fromId = max(0, (int) $this->option('from-id'));
        $dryRun = (bool) $this->option('dry-run');
        $passwordHash = $this->resolvePasswordHash();
        $now = now();

        $totalPurchasers = $this->countPurchasers($fromId);
        $this->info(sprintf(
            'Starting citizen user sync%s (%s purchaser records, chunk size %d).',
            $dryRun ? ' [DRY RUN]' : '',
            number_format($totalPurchasers),
            $chunkSize
        ));

        if ($fromId > 0) {
            $this->line("Resuming after PrivatePurchaserId {$fromId}.");
        }

        $progressBar = $this->output->createProgressBar($totalPurchasers);
        $progressBar->start();

        $lastId = $fromId;

        DB::table('property_private_purchasers')
            ->select(['PrivatePurchaserId', 'PrivatePurchaserName', 'MobileNo'])
            ->where('PrivatePurchaserId', '>', $fromId)
            ->orderBy('PrivatePurchaserId')
            ->chunkById($chunkSize, function ($purchasers) use (
                $citizenGroup,
                $citizenRole,
                $passwordHash,
                $now,
                $dryRun,
                $progressBar,
                &$lastId
            ) {
                $lastId = (int) $purchasers->last()->PrivatePurchaserId;
                $this->processed += $purchasers->count();

                if ($dryRun) {
                    $this->analyzeChunk($purchasers);
                } else {
                    DB::transaction(function () use (
                        $purchasers,
                        $citizenGroup,
                        $citizenRole,
                        $passwordHash,
                        $now
                    ) {
                        $this->processChunk($purchasers, $citizenGroup, $citizenRole, $passwordHash, $now);
                    });
                }

                $progressBar->advance($purchasers->count());
            }, 'PrivatePurchaserId', 'PrivatePurchaserId');

        $progressBar->finish();
        $this->newLine(2);

        $linkedCitizenUsers = DB::table('users')
            ->whereNotNull('private_purchaser_id')
            ->whereNull('deleted_at')
            ->count();

        $totalPurchasersInDb = DB::table('property_private_purchasers')->count();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed purchaser records', number_format($this->processed)],
                ['Users created', number_format($this->usersCreated)],
                ['Existing users linked to purchaser', number_format($this->usersLinked)],
                ['Users skipped (already linked)', number_format($this->usersSkipped)],
                ['Role mappings created', number_format($this->rolesCreated)],
                ['Role mappings skipped', number_format($this->rolesSkipped)],
                ['Citizen users linked to purchasers (DB total)', number_format($linkedCitizenUsers)],
                ['property_private_purchasers (DB total)', number_format($totalPurchasersInDb)],
            ]
        );

        if ($linkedCitizenUsers < $totalPurchasersInDb) {
            $missing = $totalPurchasersInDb - $linkedCitizenUsers;
            $this->warn("Still missing {$missing} user(s). Re-run the command or check for errors.");
        } else {
            $this->info('All purchaser records have a linked user.');
        }

        if (! $dryRun && $lastId > $fromId) {
            $this->line("Last processed PrivatePurchaserId: {$lastId}");
            $this->line("Resume with: php artisan citizens:sync-users --from-id={$lastId}");
        }

        // Find all citizen users (MMSAY) that are missing role mappings and insert them
        $this->info('Verifying and fixing missing role mappings for all MMSAY citizen users...');
        $missingRoleUserIds = DB::table('users')
            ->leftJoin('role_types', 'users.id', '=', 'role_types.user_id')
            ->where('users.scheme', 'MMSAY')
            ->whereNull('users.deleted_at')
            ->whereNull('role_types.id')
            ->pluck('users.id')
            ->all();

        if (count($missingRoleUserIds) > 0) {
            $this->info('Found ' . count($missingRoleUserIds) . ' users missing role mappings. Generating mappings...');
            $this->syncRoleMappings($missingRoleUserIds, $citizenGroup, $citizenRole, $now);
        }

        return self::SUCCESS;
    }

    private function countPurchasers(int $fromId): int
    {
        return DB::table('property_private_purchasers')
            ->where('PrivatePurchaserId', '>', $fromId)
            ->count();
    }

    private function resolvePasswordHash(): string
    {
        $configured = config('auth.citizen_default_password');

        if (is_string($configured) && $configured !== '') {
            return Hash::make($configured);
        }

        return Hash::make(Str::random(32));
    }

    /**
     * @param  Collection<int, object>  $purchasers
     */
    private function processChunk(
        Collection $purchasers,
        RoleGroup $citizenGroup,
        Role $citizenRole,
        string $passwordHash,
        $now
    ): void {
        $purchaserIds = $purchasers->pluck('PrivatePurchaserId')->map(fn ($id) => (int) $id)->all();

        $linkedPurchaserIds = DB::table('users')
            ->whereIn('private_purchaser_id', $purchaserIds)
            ->whereNull('deleted_at')
            ->pluck('private_purchaser_id')
            ->flip()
            ->all();

        $mobilesInChunk = [];

        foreach ($purchasers as $purchaser) {
            $mobile = IndianMobileNumber::normalize($purchaser->MobileNo);

            if ($mobile) {
                $mobilesInChunk[$mobile] = true;
            }
        }

        $orphanPool = $this->buildOrphanUserPool(array_keys($mobilesInChunk));
        $newUserRows = [];

        foreach ($purchasers as $purchaser) {
            $purchaserId = (int) $purchaser->PrivatePurchaserId;

            if (isset($linkedPurchaserIds[$purchaserId])) {
                $this->usersSkipped++;

                continue;
            }

            $mobile = IndianMobileNumber::normalize($purchaser->MobileNo);
            $name = trim((string) $purchaser->PrivatePurchaserName) ?: 'Citizen User';

            if ($mobile && isset($orphanPool[$mobile]) && $orphanPool[$mobile] !== []) {
                $orphanUserId = array_shift($orphanPool[$mobile]);

                DB::table('users')
                    ->where('id', $orphanUserId)
                    ->update([
                        'private_purchaser_id' => $purchaserId,
                        'name' => $name,
                        'scheme' => 'MMSAY',
                        'updated_at' => $now,
                    ]);

                $linkedPurchaserIds[$purchaserId] = true;
                $this->usersLinked++;

                continue;
            }

            $newUserRows[] = [
                'name' => $name,
                'email' => null,
                'mobile' => $mobile,
                'private_purchaser_id' => $purchaserId,
                'password' => $passwordHash,
                'role' => 'citizen',
                'scheme' => 'MMSAY',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($newUserRows !== []) {
            $beforeCount = DB::table('users')
                ->whereIn('private_purchaser_id', array_column($newUserRows, 'private_purchaser_id'))
                ->whereNull('deleted_at')
                ->count();

            foreach (array_chunk($newUserRows, 500) as $insertChunk) {
                DB::table('users')->insertOrIgnore($insertChunk);
            }

            $afterCount = DB::table('users')
                ->whereIn('private_purchaser_id', array_column($newUserRows, 'private_purchaser_id'))
                ->whereNull('deleted_at')
                ->count();

            $this->usersCreated += max(0, $afterCount - $beforeCount);
        }

        $userIds = DB::table('users')
            ->whereIn('private_purchaser_id', $purchaserIds)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->all();

        $this->syncRoleMappings($userIds, $citizenGroup, $citizenRole, $now);
    }

    /**
     * @param  Collection<int, object>  $purchasers
     */
    private function analyzeChunk(Collection $purchasers): void
    {
        $purchaserIds = $purchasers->pluck('PrivatePurchaserId')->map(fn ($id) => (int) $id)->all();

        $linkedPurchaserIds = DB::table('users')
            ->whereIn('private_purchaser_id', $purchaserIds)
            ->whereNull('deleted_at')
            ->pluck('private_purchaser_id')
            ->flip()
            ->all();

        $mobilesInChunk = [];

        foreach ($purchasers as $purchaser) {
            $mobile = IndianMobileNumber::normalize($purchaser->MobileNo);

            if ($mobile) {
                $mobilesInChunk[$mobile] = true;
            }
        }

        $orphanPool = $this->buildOrphanUserPool(array_keys($mobilesInChunk));

        foreach ($purchasers as $purchaser) {
            $purchaserId = (int) $purchaser->PrivatePurchaserId;

            if (isset($linkedPurchaserIds[$purchaserId])) {
                $this->usersSkipped++;

                continue;
            }

            $mobile = IndianMobileNumber::normalize($purchaser->MobileNo);

            if ($mobile && isset($orphanPool[$mobile]) && $orphanPool[$mobile] !== []) {
                array_shift($orphanPool[$mobile]);
                $this->usersLinked++;

                continue;
            }

            $this->usersCreated++;
        }
    }

    /**
     * @param  list<string>  $mobiles
     * @return array<string, list<int>>
     */
    private function buildOrphanUserPool(array $mobiles): array
    {
        if ($mobiles === []) {
            return [];
        }

        $pool = [];

        $orphanUsers = DB::table('users')
            ->select(['id', 'mobile'])
            ->whereIn('mobile', $mobiles)
            ->whereNull('private_purchaser_id')
            ->where('role', 'citizen')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        foreach ($orphanUsers as $orphanUser) {
            $pool[$orphanUser->mobile][] = (int) $orphanUser->id;
        }

        return $pool;
    }

    /**
     * @param  list<int>  $userIds
     */
    private function syncRoleMappings(
        array $userIds,
        RoleGroup $citizenGroup,
        Role $citizenRole,
        $now
    ): void {
        if ($userIds === []) {
            return;
        }

        $existingRoleUserIds = DB::table('role_types')
            ->whereIn('user_id', $userIds)
            ->whereNull('deleted_at')
            ->pluck('user_id')
            ->flip()
            ->all();

        $roleRows = [];

        foreach ($userIds as $userId) {
            if (isset($existingRoleUserIds[$userId])) {
                $this->rolesSkipped++;

                continue;
            }

            $roleRows[] = [
                'user_id' => $userId,
                'role_id' => $citizenRole->id,
                'role_group_id' => $citizenGroup->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($roleRows === []) {
            return;
        }

        foreach (array_chunk($roleRows, 500) as $insertChunk) {
            DB::table('role_types')->insertOrIgnore($insertChunk);
        }

        $this->rolesCreated += count($roleRows);
    }
}
