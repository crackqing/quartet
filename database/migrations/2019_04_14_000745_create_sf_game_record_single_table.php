<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSfGameRecordSingleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sf_game_record_single', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('yazhuTotal')->nullable();
			$table->bigInteger('defenTotal')->nullable();
			$table->bigInteger('yingliTotal')->nullable();
			$table->bigInteger('choushuiTotal')->nullable();
			$table->bigInteger('coinsTotal')->nullable();
			$table->bigInteger('bankTotal')->nullable();
			$table->string('timeRange', 100)->nullable();
			$table->integer('uid')->nullable()->index('uid');
			$table->bigInteger('gameBalance')->nullable();
			$table->bigInteger('bankBalance')->nullable();
			$table->timestamps();
			$table->integer('kindid')->nullable();
			$table->string('type', 100)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sf_game_record_single');
	}

}
