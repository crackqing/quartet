<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSfGameRecordDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sf_game_record_detail', function(Blueprint $table)
		{
			$table->integer('id')->unsigned();
			$table->dateTime('time')->nullable();
			$table->integer('uid')->nullable();
			$table->integer('kindid')->nullable();
			$table->bigInteger('yazhu')->nullable();
			$table->bigInteger('defen')->nullable();
			$table->integer('yingli')->nullable();
			$table->bigInteger('choushui')->nullable();
			$table->bigInteger('coins')->nullable();
			$table->bigInteger('bank')->nullable();
			$table->integer('tid')->nullable()->comment('桌子ID');
			$table->string('tname', 100)->nullable()->comment('桌子名字');
			$table->boolean('isdealer')->nullable();
			$table->string('xiazhu')->nullable()->comment('下注类型');
			$table->string('qishu')->nullable()->comment('当前期数');
			$table->string('xiazhupx')->nullable()->comment('下注牌型');
			$table->string('kaijiangpx')->nullable()->comment('开奖牌型');
			$table->index(['time','uid','kindid'], 'uid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sf_game_record_detail');
	}

}
