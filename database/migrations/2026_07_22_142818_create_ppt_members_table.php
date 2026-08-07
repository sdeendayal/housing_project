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
        Schema::create('ppt_members', function (Blueprint $table) {
            $table->id();
            
            $csvPath = database_path('seeders/data/ppt_members.csv');
            if (file_exists($csvPath)) {
                $handle = fopen($csvPath, 'r');
                $headers = fgetcsv($handle);
                fclose($handle);
                
                foreach ($headers as $header) {
                    $header = trim($header);
                    if (empty($header) || in_array($header, ['id', 'created_at', 'updated_at'])) {
                        continue;
                    }
                    $table->text($header)->nullable();
                }
            }
            
            $table->string('district')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->string('property_type', 100)->default('flat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppt_members');
    }
};
