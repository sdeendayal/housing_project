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
        Schema::create('registary', function (Blueprint $table) {
            $table->id();
            $table->string('District')->nullable();
            $table->string('TehsilName')->nullable();
            $table->string('Village')->nullable();
            $table->string('Token')->nullable()->index();
            $table->string('Khewat')->nullable();
            $table->string('FirstParty')->nullable();
            $table->string('TotalArea')->nullable();
            $table->string('Bhag')->nullable();
            $table->string('TransferArea')->nullable();
            $table->string('SecondParty')->nullable();
            $table->string('SecondPartyMobile')->nullable()->index();
            $table->string('RegistaryNumber')->nullable();
            $table->dateTime('RegistaryDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registary');
    }
};
