<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReplyToConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->text('message')->nullable()->change();
            $table->text('reply')->nullable()->change();
            $table->text('attachment')->nullable();
            $table->boolean('is_reply')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('message')->nullable();
            $table->string('reply')->nullable();
            $table->dropColumn('attachment');
            $table->dropColumn('is_reply');
        });
    }
}
