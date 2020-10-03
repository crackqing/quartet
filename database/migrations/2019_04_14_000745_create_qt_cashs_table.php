<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtCashsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_cashs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('order_id', 100)->nullable();
			$table->integer('agent_id')->unsigned()->nullable();
			$table->string('agent_nickname', 100)->nullable();
			$table->integer('cash_money')->nullable()->comment('兑换金钱');
			$table->bigInteger('coins')->nullable()->comment('当前余额帐号');
			$table->bigInteger('bank')->nullable()->comment('当前的银行钱数');
			$table->boolean('status')->nullable()->default(1)->comment('默认为1为申请中,2为已通过,3为已到帐，4为已拒绝');
			$table->timestamps();
			$table->string('exchangeType', 100)->nullable()->comment(' 兑换方式 1:支付宝,2:银行,3:微信');
			$table->string('realname', 100)->nullable();
			$table->string('account', 100)->nullable()->comment('兑换到的账号');
			$table->integer('exchangeid')->unsigned()->nullable();
			$table->integer('manager_id')->nullable()->comment('操作的管理ID，只有状态为2,3,4的情况有');
			$table->string('remark', 100)->nullable()->comment('备注操作');
			$table->index(['agent_id','created_at','status']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qt_cashs');
	}

}
