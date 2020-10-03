<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtHigchartsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_higcharts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->dateTime('date')->nullable();
			$table->integer('new')->nullable()->comment('新增玩家');
			$table->integer('login')->nullable()->comment('日活跃玩家');
			$table->float('remain_1', 10, 4)->nullable()->comment('次留人数');
			$table->float('remain_3', 10, 4)->nullable()->comment('三日留存');
			$table->float('remain_7', 10, 4)->nullable()->comment('7日留存');
			$table->integer('bind')->nullable()->comment('今日绑定ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qt_higcharts');
	}

}
