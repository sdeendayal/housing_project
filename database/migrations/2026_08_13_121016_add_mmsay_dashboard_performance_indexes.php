<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->addIndexIfMissing(
            'installment_due',
            'idx_opt_due',
            ['AssetId', 'InstallmentNumber', 'IsDeleted', 'IsActive']
        );

        $this->addIndexIfMissing(
            'cash_receipt_details',
            'idx_opt_receipts',
            ['IsDeleted', 'IsActive', 'asset_number', 'total_paid_amount']
        );

        $this->addIndexIfMissing(
            'hfa.mmsay_old_registration_data',
            'idx_opt_old_reg',
            ['districtName(50)', 'btName(50)', 'wvName(50)']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists(
            'installment_due',
            'idx_opt_due'
        );

        $this->dropIndexIfExists(
            'cash_receipt_details',
            'idx_opt_receipts'
        );

        $this->dropIndexIfExists(
            'hfa.mmsay_old_registration_data',
            'idx_opt_old_reg'
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
            ->map(function (string $column) {
                if (preg_match('/^([a-zA-Z0-9_]+)\((\d+)\)$/', $column, $matches)) {
                    return '`' . str_replace('`', '``', $matches[1]) . '`(' . $matches[2] . ')';
                }
                return '`' . str_replace('`', '``', $column) . '`';
            })
            ->implode(', ');

        DB::statement(
            sprintf(
                'ALTER TABLE `%s` ADD INDEX `%s` (%s)',
                str_replace('.', '`.`', str_replace('`', '``', $table)),
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
                str_replace('.', '`.`', str_replace('`', '``', $table)),
                str_replace('`', '``', $indexName)
            )
        );
    }

    private function indexExists(
        string $table,
        string $indexName
    ): bool {
        $parts = explode('.', $table);
        $schema = count($parts) > 1 ? $parts[0] : null;
        $tableName = count($parts) > 1 ? $parts[1] : $table;

        $query = DB::table('information_schema.statistics');
        if ($schema) {
            $query->where('TABLE_SCHEMA', $schema);
        } else {
            $query->whereRaw('TABLE_SCHEMA = DATABASE()');
        }
        return $query->where('TABLE_NAME', $tableName)
            ->where('INDEX_NAME', $indexName)
            ->exists();
    }
};
