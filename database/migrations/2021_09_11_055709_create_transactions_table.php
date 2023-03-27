<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->decimal('old_balance', 13)->unsigned();
            $table->decimal('transaction_amount', 13);
            $table->decimal('new_balance', 13)->unsigned();
            $table->string('type', 20)->nullable();
            $table->string('orderable_type', 30)->nullable();
            $table->unsignedBigInteger('orderable_id')->nullable();
            $table->timestamps();
            $table->index(['orderable_type', 'orderable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
