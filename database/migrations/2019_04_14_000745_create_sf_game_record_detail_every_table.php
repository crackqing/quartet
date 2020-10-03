<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSfGameRecordDetailEveryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sf_game_record_detail_every', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('yazhu')->nullable();
			$table->bigInteger('defen')->nullable();
			$table->bigInteger('choushui')->nullable();
			$table->dateTime('start_time')->nullable();
			$table->dateTime('end_time')->nullable();
			$table->integer('kindid')->nullable();
			$table->integer('tid')->nullable();
			$table->timestamps();
			$table->integer('uid')->nullable()->comment('单个玩家每5分钟的统计');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sf_game_record_detail_every');
	}

}
