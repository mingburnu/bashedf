<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorizersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorizers', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->boolean('supervising')->default(1);
            $table->string('api')->nullable();
            $table->string('boolean_index', 20)->nullable();
            $table->text('additional_parameters')->nullable();
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
        Schema::dropIfExists('authorizers');
    }
}
