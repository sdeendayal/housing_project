<?php

use App\Models\Grievance;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grievances', function (Blueprint $table) {
            $table->string('ticket_number', 30)->nullable()->unique()->after('secure_id');
        });

        Grievance::query()
            ->whereNull('ticket_number')
            ->orderBy('id')
            ->each(function (Grievance $grievance) {
                $grievance->update([
                    'ticket_number' => Grievance::generateTicketNumber(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('grievances', function (Blueprint $table) {
            $table->dropColumn('ticket_number');
        });
    }
};
