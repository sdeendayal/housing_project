<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grievances', function (Blueprint $table) {
            $table->id();
            $table->char('secure_id', 32)->unique();
            $table->string('application_id', 100);
            $table->string('applicant_name', 200);
            $table->string('mobile_number', 15);
            $table->string('grievance_subject', 255);
            $table->text('grievance_description');
            $table->string('grievance_status', 50)->default('Pending');
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grievances');
    }
};
