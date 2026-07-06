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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->string('merchant_txn_no')->unique()->index();
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('PENDING');
            $table->string('gateway_txn_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('response_code')->nullable();
            $table->string('response_description')->nullable();
            $table->text('request_payload_dump')->nullable();
            $table->text('response_payload_dump')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
