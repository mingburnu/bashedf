<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->decimal('min_deposit_amount', 11, 0)->unsigned();
            $table->decimal('max_deposit_amount', 11, 0)->unsigned();
            $table->decimal('min_payment_amount', 11, 0)->unsigned();
            $table->decimal('max_payment_amount', 11, 0)->unsigned();
            $table->decimal('deposit_processing_fee_percent', 7, 4)->unsigned();
            $table->decimal('payment_processing_fee', 9)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
