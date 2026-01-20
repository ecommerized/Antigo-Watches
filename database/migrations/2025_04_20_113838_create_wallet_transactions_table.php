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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            // Polymorphic relation to Customer, Admin, Vendor, etc.
            $table->morphs('walletable'); // walletable_id & walletable_type
            $table->string('type');
            $table->string('direction');
            // Amount and balances
            $table->decimal('amount', 24, 8);
            $table->decimal('opening_balance', 24, 8);
            $table->decimal('closing_balance', 24, 8);

            // Optional meta info
            $table->string('reference')->nullable(); // e.g., order id, payment id
            $table->string('method')->nullable(); // e.g., bkash, card, system, etc.
            $table->text('description')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
