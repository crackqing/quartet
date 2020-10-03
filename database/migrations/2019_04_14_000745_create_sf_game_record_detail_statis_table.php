<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSfGameRecordDetailStatisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sf_game_record_detail_statis', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('yazhuTotal')->nullable();
			$table->bigInteger('defenTotal')->nullable();
			$table->bigInteger('yingliTotal')->nullable();
			$table->bigInteger('choushuiTotal')->nullable();
			$table->bigInteger('coinsTotal')->nullable();
			$table->bigInteger('bankTotal')->nullable();
			$table->dateTime('time')->nullable();
			$table->integer('kindid')->nullable()->comment('单个游戏的统计，与总的游戏统计.');
			$table->integer('tid')->nullable()->comment('桌子ID');
			$table->integer('uid')->nullable()->comment('用户ID，单个ID的统计');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sf_game_record_detail_statis');
	}

}
