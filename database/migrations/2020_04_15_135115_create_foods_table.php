<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('alias')->nullable();
            $table->string('description');
            $table->integer('kcal');
            $table->integer('fat');
            $table->integer('protein');
            $table->integer('carbohydrate');
            $table->integer('potassium');
            $table->integer('base_quantity');
            $table->unsignedBigInteger('foodsource_id');
            $table->unsignedBigInteger('foodgroup_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('foodgroup_id')->on('foodgroups')->references('id');
            $table->foreign('foodsource_id')->on('foodsources')->references('id');
            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foods');
    }
}
