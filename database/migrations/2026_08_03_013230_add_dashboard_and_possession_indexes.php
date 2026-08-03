<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | OwnerMaster
        |--------------------------------------------------------------------------
        */

        $this->addIndexIfMissing(
            'OwnerMaster',
            'idx_owner_block_phase_village',
            ['BlockId', 'Phase', 'VillageId']
        );

        /*
        |--------------------------------------------------------------------------
        | VillageMaster
        |--------------------------------------------------------------------------
        */

        $this->addIndexIfMissing(
            'VillageMaster',
            'idx_village_dashboard_filters',
            [
                'DistrictId',
                'BlockId',
                'Phase',
                'plots',
                'VillageId',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | registary
        |--------------------------------------------------------------------------
        */

        $this->addIndexIfMissing(
            'registary',
            'idx_registry_second_party_mobile',
            ['SecondPartyMobile']
        );

        /*
        |--------------------------------------------------------------------------
        | mmgay_possession_applications
        |--------------------------------------------------------------------------
        */

        $this->addIndexIfMissing(
            'mmgay_possession_applications',
            'idx_possession_owner_status',
            [
                'owner_id',
                'physical_possession_status',
            ]
        );

        $this->addIndexIfMissing(
            'mmgay_possession_applications',
            'idx_possession_status_updated',
            [
                'physical_possession_status',
                'updated_at',
            ]
        );

        $this->addIndexIfMissing(
            'mmgay_possession_applications',
            'idx_possession_secure_id',
            ['secure_id']
        );

        /*
        |--------------------------------------------------------------------------
        | FlatMaster
        |--------------------------------------------------------------------------
        | FlatId पहले से PRIMARY/INDEX हो तो यह skip हो जाएगा।
        |--------------------------------------------------------------------------
        */

        $this->addIndexIfMissing(
            'FlatMaster',
            'idx_flatmaster_flat_id',
            ['FlatId']
        );
    }

    public function down(): void
    {
        $this->dropIndexIfExists(
            'OwnerMaster',
            'idx_owner_block_phase_village'
        );

        $this->dropIndexIfExists(
            'VillageMaster',
            'idx_village_dashboard_filters'
        );

        $this->dropIndexIfExists(
            'registary',
            'idx_registry_second_party_mobile'
        );

        $this->dropIndexIfExists(
            'mmgay_possession_applications',
            'idx_possession_owner_status'
        );

        $this->dropIndexIfExists(
            'mmgay_possession_applications',
            'idx_possession_status_updated'
        );

        $this->dropIndexIfExists(
            'mmgay_possession_applications',
            'idx_possession_secure_id'
        );

        $this->dropIndexIfExists(
            'FlatMaster',
            'idx_flatmaster_flat_id'
        );
    }

    private function addIndexIfMissing(
        string $table,
        string $indexName,
        array $columns
    ): void {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        $quotedColumns = collect($columns)
            ->map(
                fn(string $column) =>
                    '`' . str_replace('`', '``', $column) . '`'
            )
            ->implode(', ');

        DB::statement(
            sprintf(
                'ALTER TABLE `%s` ADD INDEX `%s` (%s)',
                str_replace('`', '``', $table),
                str_replace('`', '``', $indexName),
                $quotedColumns
            )
        );
    }

    private function dropIndexIfExists(
        string $table,
        string $indexName
    ): void {
        if (!$this->indexExists($table, $indexName)) {
            return;
        }

        DB::statement(
            sprintf(
                'ALTER TABLE `%s` DROP INDEX `%s`',
                str_replace('`', '``', $table),
                str_replace('`', '``', $indexName)
            )
        );
    }

    private function indexExists(
        string $table,
        string $indexName
    ): bool {
        return DB::table('information_schema.statistics')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $indexName)
            ->exists();
    }
};