<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->indexExists('users', 'users_mobile_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['mobile']);
            });
        }

        if (! Schema::hasColumn('users', 'private_purchaser_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('private_purchaser_id')->nullable()->after('id');
            });
        } else {
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE users MODIFY private_purchaser_id INT NULL');
            }
        }

        if (! $this->indexExists('users', 'users_private_purchaser_id_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('private_purchaser_id', 'users_private_purchaser_id_unique');
            });
        }

        if (! $this->indexExists('users', 'users_mobile_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('mobile', 'users_mobile_index');
            });
        }

        if (! $this->foreignKeyExists('users', 'users_private_purchaser_id_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('private_purchaser_id')
                    ->references('PrivatePurchaserId')
                    ->on('property_private_purchasers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if ($this->foreignKeyExists('users', 'users_private_purchaser_id_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['private_purchaser_id']);
            });
        }

        if ($this->indexExists('users', 'users_mobile_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_mobile_index');
            });
        }

        if ($this->indexExists('users', 'users_private_purchaser_id_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_private_purchaser_id_unique');
            });
        }

        if (Schema::hasColumn('users', 'private_purchaser_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('private_purchaser_id');
            });
        }

        if (! $this->indexExists('users', 'users_mobile_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('mobile');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? '') === $indexName) {
                return true;
            }
        }

        return false;
    }

    private function foreignKeyExists(string $table, string $foreignKeyName): bool
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            if (($foreignKey['name'] ?? '') === $foreignKeyName) {
                return true;
            }
        }

        return false;
    }
};
