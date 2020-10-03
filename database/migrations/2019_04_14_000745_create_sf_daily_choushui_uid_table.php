<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSfDailyChoushuiUidTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sf_daily_choushui_uid', function(Blueprint $table)
		{
			$table->increments('id');
			$table->float('total_choushui', 15)->comment('统计昨天的抽水,默认只能运行一次.');
			$table->string('time_rand', 100)->comment('时间的区间记录');
			$table->integer('uid')->comment('统计UID');
			$table->integer('is_dealer')->unsigned()->comment('是否为币商');
			$table->decimal('performance', 10, 4)->comment('返利的倍数 * 对应的数值 ');
			$table->integer('multiple')->comment('返利的倍数');
			$table->integer('return_gold')->comment('返利的金钱');
			$table->integer('bind_id')->comment('对应绑定ID');
			$table->date('time');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sf_daily_choushui_uid');
	}

}
