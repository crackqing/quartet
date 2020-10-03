<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('price')->unsigned()->nullable()->comment('价格单位为分来进行处理');
			$table->string('order_id', 100)->nullable()->comment('订单ID');
			$table->integer('agent_id')->nullable()->comment('充值的游戏UID');
			$table->string('pay_type', 20)->nullable()->default('0')->comment('1为默认的订单充值,2待定');
			$table->bigInteger('before_coins')->nullable()->comment('之前钱数');
			$table->bigInteger('coins')->nullable()->comment('当前的余额金币');
			$table->bigInteger('bank')->nullable()->comment('保险箱的数值');
			$table->timestamps();
			$table->integer('manager_id')->nullable()->comment('管理ID');
			$table->string('remark', 50)->nullable()->comment('备注');
			$table->index(['agent_id','created_at','order_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qt_orders');
	}

}
