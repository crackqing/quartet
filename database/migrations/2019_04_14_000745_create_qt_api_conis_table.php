<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtApiConisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_api_conis', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uid')->nullable();
			$table->integer('manager_id')->nullable()->comment('充值的管理ID');
			$table->bigInteger('coins')->nullable()->comment('充值的金币数');
			$table->bigInteger('bank')->nullable();
			$table->boolean('type')->nullable()->comment('type 6=赠送，7=充值,11平台扣除');
			$table->string('remark', 100)->nullable()->comment('备注充值标识');
			$table->bigInteger('before_coins')->nullable()->comment('赠送前的余额');
			$table->bigInteger('balance')->nullable();
			$table->timestamps();
			$table->string('ip', 100)->nullable()->comment('ip地址');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qt_api_conis');
	}

}
