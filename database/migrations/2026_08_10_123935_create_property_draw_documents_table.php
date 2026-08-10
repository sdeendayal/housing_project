<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_draw_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_code')->nullable();
            $table->string('scheme')->nullable();
            $table->string('title')->nullable();
            $table->integer('district_id')->nullable();
            $table->string('district_name')->nullable();
            $table->string('location_label')->nullable();
            $table->string('sector_label')->nullable();
            $table->integer('total_plots')->nullable();
            $table->string('original_file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->dateTime('published_date')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('IsActive')->default(true);
            $table->boolean('IsDeleted')->default(false);
            $table->dateTime('CreatedDate')->nullable();
            $table->integer('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable();
            $table->integer('ModifiedBy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_draw_documents');
    }
};
