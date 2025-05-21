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
        Schema::create('bank_statement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallet');
            $table->tinyText('type_transaction')->nullable(false);
            $table->decimal('amount_transaction', 10, 2)->nullable(false);
            $table->foreignId('transfer_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement');
    }
};
