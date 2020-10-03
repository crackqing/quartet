<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtDailyBillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_daily_bills', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('off_line')->nullable()->comment('线下充值');
			$table->integer('online')->nullable()->comment('线上充值');
			$table->integer('coins')->nullable()->comment('赠送');
			$table->integer('cashs')->nullable()->comment('兑换');
			$table->bigInteger('account')->nullable()->comment('游戏账号总余额');
			$table->bigInteger('bank')->nullable()->comment('游戏银行余额');
			$table->timestamps();
			$table->integer('agent_id')->nullable();
			$table->string('type', 100)->nullable();
			$table->dateTime('time')->nullable()->comment('昨天的统计时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qt_daily_bills');
	}

}
