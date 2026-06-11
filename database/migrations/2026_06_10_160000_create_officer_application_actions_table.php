<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officer_application_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')
                ->unique()
                ->constrained('physical_possession_applications')
                ->cascadeOnDelete();
            $table->foreignId('officer_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->text('remarks');
            $table->string('previous_status')->default('pending');
            $table->string('new_status');
            $table->string('application_number');
            $table->integer('district_id')->nullable();
            $table->string('district_name')->nullable();
            $table->timestamps();

            $table->index(['officer_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_application_actions');
    }
};
