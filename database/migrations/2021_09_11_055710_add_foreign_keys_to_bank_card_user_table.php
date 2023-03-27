<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToBankCardUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_card_user', function (Blueprint $table) {
            $table->foreign('bank_card_id')->references('id')->on('bank_cards');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_card_user', function (Blueprint $table) {
            $table->dropForeign('bank_card_user_bank_card_id_foreign');
            $table->dropForeign('bank_card_user_user_id_foreign');
        });
    }
}
