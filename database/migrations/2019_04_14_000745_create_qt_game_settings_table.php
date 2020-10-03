<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtGameSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_game_settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('kindid')->nullable()->comment('游戏ID');
			$table->integer('tid')->nullable();
			$table->decimal('tax', 8, 4)->unsigned()->nullable()->comment('1000的游戏桌子设置');
			$table->smallInteger('type')->nullable()->comment('场地类型');
			$table->smallInteger('difficulty')->nullable()->comment('游戏难度 ');
			$table->string('name', 100)->nullable();
			$table->smallInteger('tableType')->nullable()->comment('0为 新手');
			$table->bigInteger('enterLimit')->nullable()->comment('最小携带金币');
			$table->bigInteger('minCannon')->nullable()->comment('最小炮值');
			$table->bigInteger('maxCannon')->nullable();
			$table->boolean('enable')->nullable()->comment('开关0为关1，为开的状态');
			$table->integer('extend_1')->nullable()->comment('扩展字段1，预先保留');
			$table->integer('extend_2')->nullable();
			$table->integer('extend_3')->nullable();
			$table->integer('extend_4')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qt_game_settings');
	}

}
