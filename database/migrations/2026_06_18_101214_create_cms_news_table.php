<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cms_news', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->longText('description')->nullable();

            $table->enum('type', ['image', 'pdf', 'link']);

            $table->string('image')->nullable();
            $table->string('pdf')->nullable();

            $table->string('link')->nullable();

            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_news');
    }
};
