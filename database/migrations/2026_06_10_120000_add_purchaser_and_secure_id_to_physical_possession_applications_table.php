<?php

use App\Models\PhysicalPossessionApplication;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('physical_possession_applications', 'secure_id')) {
            Schema::table('physical_possession_applications', function (Blueprint $table) {
                $table->char('secure_id', 32)->nullable()->unique()->after('id');
                $table->integer('private_purchaser_id')->nullable()->after('user_id');
                $table->string('ppp_id', 50)->nullable()->after('private_purchaser_id');
                $table->string('member_id', 50)->nullable()->after('ppp_id');
            });
        } else {
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE physical_possession_applications MODIFY private_purchaser_id INT NULL');
            }
        }

        PhysicalPossessionApplication::query()
            ->whereNull('secure_id')
            ->orderBy('id')
            ->each(function (PhysicalPossessionApplication $application) {
                $application->update([
                    'secure_id' => PhysicalPossessionApplication::generateSecureId(),
                ]);
            });

        if (DB::connection()->getDriverName() === 'mysql') {
            $foreignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'physical_possession_applications'
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                  AND CONSTRAINT_NAME = 'physical_possession_applications_private_purchaser_id_foreign'
            "));

            if ($foreignKeys->isEmpty()) {
                Schema::table('physical_possession_applications', function (Blueprint $table) {
                    $table->foreign('private_purchaser_id')
                        ->references('PrivatePurchaserId')
                        ->on('property_private_purchasers')
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('physical_possession_applications', function (Blueprint $table) {
            if (Schema::hasColumn('physical_possession_applications', 'private_purchaser_id')) {
                $table->dropForeign(['private_purchaser_id']);
            }
            $table->dropColumn(['secure_id', 'private_purchaser_id', 'ppp_id', 'member_id']);
        });
    }
};
