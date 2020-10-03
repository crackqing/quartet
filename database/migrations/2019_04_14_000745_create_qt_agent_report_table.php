<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtAgentReportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_agent_report', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('directly_pay')->nullable()->comment('有充值与兑换 根据status 1,2,3,4来区分');
			$table->integer('directly_not_pay')->nullable();
			$table->boolean('status')->nullable()->default(0)->comment('1为充值，2为兑换，3为游戏记录 赠送记录待定');
			$table->bigInteger('directly_ya')->nullable();
			$table->bigInteger('directly_de')->nullable();
			$table->bigInteger('directly_yl')->nullable();
			$table->bigInteger('directly_not_ya')->nullable();
			$table->bigInteger('directly_not_de')->nullable();
			$table->bigInteger('directly_not_yl')->nullable();
			$table->timestamps();
			$table->integer('uid')->nullable()->comment('后台的代理登录用户');
			$table->dateTime('time')->nullable();
			$table->integer('kindid')->nullable()->comment('区分管理的所有用户单个游戏的情况');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qt_agent_report');
	}

}
