<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSfWeekChoushuiAgentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sf_week_choushui_agent', function(Blueprint $table)
		{
			$table->increments('id');
			$table->float('total_choushui', 15);
			$table->string('time_rand', 100);
			$table->integer('uid');
			$table->decimal('performance', 10, 4);
			$table->integer('multiple');
			$table->integer('return_gold');
			$table->integer('receive');
			$table->integer('payable');
			$table->date('time');
			$table->integer('team_receive');
			$table->integer('level');
			$table->integer('bind_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sf_week_choushui_agent');
	}

}
