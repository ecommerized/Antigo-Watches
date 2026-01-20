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
        Schema::table('orders', function (Blueprint $table) {
            $table->json('additional_payment_amount')->nullable()->after('additional_payment_method');
        });

        DB::table('orders')->whereNull('additional_payment_amount')->update([
            'additional_payment_amount' => json_encode([])
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('additional_payment_amount');
        });
    }
};
