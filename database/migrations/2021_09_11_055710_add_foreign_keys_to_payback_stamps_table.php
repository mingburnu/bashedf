<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPaybackStampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payback_stamps', function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payback_stamps', function (Blueprint $table) {
            $table->dropForeign('payback_stamps_admin_id_foreign');
            $table->dropForeign('payback_stamps_payment_id_foreign');
        });
    }
}
