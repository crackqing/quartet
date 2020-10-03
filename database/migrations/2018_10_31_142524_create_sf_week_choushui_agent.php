<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSfWeekChoushuiAgent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_week_choushui_agent', function (Blueprint $table) {
            $table->increments('id');
            $table->double('total_choushui',15,2);
            $table->string('time_rand', 100);
            $table->integer('uid');
            $table->integer('performance');
            $table->integer('multiple');
            $table->integer('return_gold');
            $table->integer('receive');
            $table->integer('payable');
            $table->date('time');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sf_week_choushui_agent');
    }
}
