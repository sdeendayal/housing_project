<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('physical_possession_applications', 'asset_id')) {
                $table->integer('asset_id')->nullable()->after('private_purchaser_id');
                $table->foreign('asset_id')
                    ->references('AssetId')
                    ->on('property_registration')
                    ->nullOnDelete();
            }
        });

        Schema::table('physical_possession_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('physical_possession_documents', 'asset_id')) {
                $table->integer('asset_id')->nullable()->after('application_id');
            }
        });

        Schema::table('application_status_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('application_status_logs', 'asset_id')) {
                $table->integer('asset_id')->nullable()->after('application_id');
            }
        });

        if ($this->foreignKeyExists('officer_application_actions', 'officer_application_actions_application_id_foreign')) {
            Schema::table('officer_application_actions', function (Blueprint $table) {
                $table->dropForeign(['application_id']);
            });
        }

        $this->dropUniqueIfExists('officer_application_actions', 'officer_application_actions_application_id_unique');

        Schema::table('officer_application_actions', function (Blueprint $table) {
            if (! $this->foreignKeyExists('officer_application_actions', 'officer_application_actions_application_id_foreign')) {
                $table->foreign('application_id')
                    ->references('id')
                    ->on('physical_possession_applications')
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('officer_application_actions', 'asset_id')) {
                $table->integer('asset_id')->nullable()->after('application_id');
            }
            if (! Schema::hasColumn('officer_application_actions', 'private_purchaser_id')) {
                $table->integer('private_purchaser_id')->nullable()->after('asset_id');
            }
            if (! Schema::hasColumn('officer_application_actions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('private_purchaser_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('officer_application_actions', 'secure_id')) {
                $table->char('secure_id', 32)->nullable()->after('user_id');
            }
        });

        $this->backfillAssetIds();
    }

    public function down(): void
    {
        Schema::table('officer_application_actions', function (Blueprint $table) {
            if (Schema::hasColumn('officer_application_actions', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn(['asset_id', 'private_purchaser_id', 'user_id', 'secure_id']);
            }
            $table->unique('application_id');
        });

        Schema::table('application_status_logs', function (Blueprint $table) {
            $table->dropColumn('asset_id');
        });

        Schema::table('physical_possession_documents', function (Blueprint $table) {
            $table->dropColumn('asset_id');
        });

        Schema::table('physical_possession_applications', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropColumn('asset_id');
        });
    }

    private function backfillAssetIds(): void
    {
        $applications = DB::table('physical_possession_applications')
            ->select('id', 'private_purchaser_id', 'secure_id', 'user_id', 'asset_id')
            ->get();

        foreach ($applications as $application) {
            $assetId = $application->asset_id ?: $this->resolveAssetId($application->private_purchaser_id);

            if (! $assetId) {
                continue;
            }

            DB::table('physical_possession_applications')
                ->where('id', $application->id)
                ->update(['asset_id' => $assetId]);

            DB::table('physical_possession_documents')
                ->where('application_id', $application->id)
                ->update(['asset_id' => $assetId]);

            DB::table('application_status_logs')
                ->where('application_id', $application->id)
                ->update(['asset_id' => $assetId]);

            DB::table('officer_application_actions')
                ->where('application_id', $application->id)
                ->update([
                    'asset_id' => $assetId,
                    'private_purchaser_id' => $application->private_purchaser_id,
                    'user_id' => $application->user_id,
                    'secure_id' => $application->secure_id,
                ]);
        }
    }

    private function resolveAssetId(?int $privatePurchaserId): ?int
    {
        if (! $privatePurchaserId) {
            return null;
        }

        $assetId = DB::table('property_auction_detail')
            ->where('PurchaserID', $privatePurchaserId)
            ->where('IsDeleted', 0)
            ->where('IsActive', 1)
            ->orderByDesc('CreatedDate')
            ->value('AssetId');

        return $assetId ? (int) $assetId : null;
    }

    private function dropUniqueIfExists(string $table, string $indexName): void
    {
        $exists = false;
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? '') === $indexName) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                $blueprint->dropIndex($indexName);
            });
        }
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            if (($foreignKey['name'] ?? '') === $constraintName) {
                return true;
            }
        }

        return false;
    }
};
