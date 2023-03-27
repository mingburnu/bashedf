<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id', 40)->unique();
            $table->unsignedInteger('user_id');
            $table->string('account_name');
            $table->string('customized_id', 36)->nullable();
            $table->string('bank_name', 140);
            $table->string('account_number', 30);
            $table->string('branch')->nullable();
            $table->decimal('amount', 13)->unsigned();
            $table->decimal('processing_fee', 9)->unsigned();
            $table->decimal('total_amount', 13)->unsigned();
            $table->tinyInteger('status');
            $table->string('callback_url')->nullable();
            $table->unsignedInteger('admin_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'customized_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
