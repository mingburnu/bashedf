<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id', 40)->unique();
            $table->unsignedInteger('user_id');
            $table->decimal('amount', 13)->unsigned();
            $table->decimal('processing_fee', 13)->unsigned();
            $table->decimal('total_amount', 13)->unsigned();
            $table->string('account_name')->nullable();
            $table->string('bank_name', 140);
            $table->string('account_number', 30)->nullable();
            $table->string('bank_district')->nullable();
            $table->string('bank_address')->nullable();
            $table->tinyInteger('status');
            $table->unsignedInteger('admin_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
