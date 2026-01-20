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
        DB::statement('SET SESSION sql_mode = CONCAT(@@SESSION.sql_mode, ",NO_AUTO_VALUE_ON_ZERO");');
        DB::statement('INSERT IGNORE INTO users (id, f_name, l_name, created_at, updated_at)
               VALUES (0, "Walk-In", "Customer", NOW(), NOW());');
        DB::statement('SET SESSION sql_mode = REPLACE(@@SESSION.sql_mode, "NO_AUTO_VALUE_ON_ZERO", "");');

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::table('users')->where('id', 0)->delete();
        });
    }
};
