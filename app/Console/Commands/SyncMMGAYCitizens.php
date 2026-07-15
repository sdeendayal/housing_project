<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\RoleGroup;
use App\Support\IndianMobileNumber;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SyncMMGAYCitizens extends Command
{
    protected $signature = 'citizens:sync-mmgay-users
                            {--chunk=5000 : Number of owner records per batch}
                            {--dry-run : Report actions without writing to the database}';

    protected $description = 'Sync every ownermaster row into users (one user per unique mobile under MMGAY scheme)';

    private int $processed = 0;
    private int $usersCreated = 0;
    private int $usersSkipped = 0;
    private int $rolesCreated = 0;

    public function handle(): int
    {
        ini_set('memory_limit', '2048M');

        $citizenRole = Role::where('slug', 'villager')->first();

        if (! $citizenRole) {
            $this->error('Villager role not found. Run RoleSeeder first.');

            return self::FAILURE;
        }

        $chunkSize = max(100, (int) $this->option('chunk'));
        $dryRun = (bool) $this->option('dry-run');
        $passwordHash = $this->resolvePasswordHash();
        $now = now();

        $totalOwners = DB::table('ownermaster')->count();

        if (!$dryRun) {
            $this->info('Cleaning up existing MMGAY citizen users to perform a clean sync...');
            DB::statement('SET foreign_key_checks = 0');
            
            // Get IDs of MMGAY citizen users to delete their role types
            $mmgayUserIds = DB::table('users')
                ->where('scheme', 'MMGAY')
                ->where('role', 'villager')
                ->pluck('id')
                ->all();
                
            if (count($mmgayUserIds) > 0) {
                foreach (array_chunk($mmgayUserIds, 5000) as $userIdChunk) {
                    DB::table('role_types')->whereIn('user_id', $userIdChunk)->delete();
                    DB::table('users')->whereIn('id', $userIdChunk)->delete();
                }
            }
            
            DB::statement('SET foreign_key_checks = 1');
            $this->info('Cleanup complete.');
        }

        $this->info(sprintf(
            'Starting MMGAY citizen user sync%s (%s owner records, chunk size %d).',
            $dryRun ? ' [DRY RUN]' : '',
            number_format($totalOwners),
            $chunkSize
        ));

        $progressBar = $this->output->createProgressBar($totalOwners);
        $progressBar->start();

        // Load all districts and blocks for lookup
        $districts = DB::table('districtmaster')->pluck('DistrictName', 'DistrictId')->all();
        $blocks = DB::table('blockmaster')->pluck('BlockName', 'BlockId')->all();

        DB::table('ownermaster')
            ->select(['OwnerId', 'OwnerName', 'MobileNo', 'DistrictId', 'BlockId'])
            ->orderBy('OwnerId')
            ->chunkById($chunkSize, function ($owners) use (
                $citizenRole,
                $passwordHash,
                $now,
                $dryRun,
                $progressBar,
                $districts,
                $blocks
            ) {
                $this->processed += $owners->count();

                if (!$dryRun) {
                    DB::transaction(function () use (
                        $owners,
                        $citizenRole,
                        $passwordHash,
                        $now,
                        $districts,
                        $blocks
                    ) {
                        $this->processChunk($owners, $citizenRole, $passwordHash, $now, $districts, $blocks);
                    });
                } else {
                    $this->analyzeChunk($owners);
                }

                $progressBar->advance($owners->count());
            }, 'OwnerId', 'OwnerId');

        $progressBar->finish();
        $this->newLine(2);

        // Find all MMGAY users that are missing role mappings and insert them
        $this->info('Verifying and fixing missing role mappings for all MMGAY users...');
        $missingRoleUserIds = DB::table('users')
            ->leftJoin('role_types', 'users.id', '=', 'role_types.user_id')
            ->where('users.scheme', 'MMGAY')
            ->whereNull('users.deleted_at')
            ->whereNull('role_types.id')
            ->pluck('users.id')
            ->all();

        if (count($missingRoleUserIds) > 0) {
            $this->info('Found ' . count($missingRoleUserIds) . ' users missing role mappings. Generating mappings...');
            $this->syncRoleMappings($missingRoleUserIds, $citizenRole, $now);
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed owner records', number_format($this->processed)],
                ['Users created', number_format($this->usersCreated)],
                ['Users skipped (already exists or invalid mobile)', number_format($this->usersSkipped)],
                ['Role mappings created/verified', number_format($this->rolesCreated)],
            ]
        );

        return self::SUCCESS;
    }

    private function resolvePasswordHash(): string
    {
        $configured = config('auth.citizen_default_password');

        if (is_string($configured) && $configured !== '') {
            return Hash::make($configured);
        }

        return Hash::make('123456');
    }

    private function processChunk(
        Collection $owners,
        Role $citizenRole,
        string $passwordHash,
        $now,
        array $districts,
        array $blocks
    ): void {
        $mobilesInChunk = [];

        foreach ($owners as $owner) {
            $mobile = IndianMobileNumber::normalize($owner->MobileNo);
            if ($mobile) {
                $mobilesInChunk[$mobile] = true;
            }
        }

        if (empty($mobilesInChunk)) {
            $this->usersSkipped += $owners->count();
            return;
        }

        // Get existing MMGAY users with these mobiles in DB
        $existingMobiles = DB::table('users')
            ->where('scheme', 'MMGAY')
            ->whereIn('mobile', array_keys($mobilesInChunk))
            ->whereNull('deleted_at')
            ->pluck('mobile')
            ->flip()
            ->all();

        $newUserRows = [];
        $localInsertedMobiles = [];

        foreach ($owners as $owner) {
            $mobile = IndianMobileNumber::normalize($owner->MobileNo);

            if (!$mobile) {
                $this->usersSkipped++;
                continue;
            }

            if (isset($existingMobiles[$mobile]) || isset($localInsertedMobiles[$mobile])) {
                $this->usersSkipped++;
                continue;
            }

            $districtName = $districts[$owner->DistrictId] ?? null;
            $blockName = $blocks[$owner->BlockId] ?? null;
            $name = trim((string) $owner->OwnerName) ?: 'Citizen User';

            $newUserRows[] = [
                'name' => $name,
                'email' => null,
                'mobile' => $mobile,
                'password' => $passwordHash,
                'role' => 'villager',
                'scheme' => 'MMGAY',
                'district_id' => $owner->DistrictId,
                'district_name' => $districtName,
                'block_id' => $owner->BlockId,
                'block_name' => $blockName,
                'Is_Active' => '1',
                'Is_Deleted' => '0',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $localInsertedMobiles[$mobile] = true;
        }

        if ($newUserRows !== []) {
            $beforeCount = DB::table('users')
                ->where('scheme', 'MMGAY')
                ->whereIn('mobile', array_keys($localInsertedMobiles))
                ->whereNull('deleted_at')
                ->count();

            foreach (array_chunk($newUserRows, 500) as $insertChunk) {
                DB::table('users')->insertOrIgnore($insertChunk);
            }

            $afterCount = DB::table('users')
                ->where('scheme', 'MMGAY')
                ->whereIn('mobile', array_keys($localInsertedMobiles))
                ->whereNull('deleted_at')
                ->count();

            $createdInThisChunk = max(0, $afterCount - $beforeCount);
            $this->usersCreated += $createdInThisChunk;

            // Fetch IDs of the inserted users to create role types
            $userIds = DB::table('users')
                ->where('scheme', 'MMGAY')
                ->whereIn('mobile', array_keys($localInsertedMobiles))
                ->whereNull('deleted_at')
                ->pluck('id')
                ->all();

            $this->syncRoleMappings($userIds, $citizenRole, $now);
        }
    }

    private function analyzeChunk(Collection $owners): void
    {
        $mobilesInChunk = [];

        foreach ($owners as $owner) {
            $mobile = IndianMobileNumber::normalize($owner->MobileNo);
            if ($mobile) {
                $mobilesInChunk[$mobile] = true;
            }
        }

        if (empty($mobilesInChunk)) {
            $this->usersSkipped += $owners->count();
            return;
        }

        $existingMobiles = DB::table('users')
            ->where('scheme', 'MMGAY')
            ->whereIn('mobile', array_keys($mobilesInChunk))
            ->whereNull('deleted_at')
            ->pluck('mobile')
            ->flip()
            ->all();

        $localInsertedMobiles = [];

        foreach ($owners as $owner) {
            $mobile = IndianMobileNumber::normalize($owner->MobileNo);

            if (!$mobile) {
                $this->usersSkipped++;
                continue;
            }

            if (isset($existingMobiles[$mobile]) || isset($localInsertedMobiles[$mobile])) {
                $this->usersSkipped++;
                continue;
            }

            $this->usersCreated++;
            $localInsertedMobiles[$mobile] = true;
        }
    }

    private function syncRoleMappings(
        array $userIds,
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
                continue;
            }

            $roleRows[] = [
                'user_id' => $userId,
                'role_id' => $citizenRole->id,
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
