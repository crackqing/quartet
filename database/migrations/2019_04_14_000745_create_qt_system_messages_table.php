<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtSystemMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_system_messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uid');
			$table->string('title')->nullable();
			$table->string('content', 500);
			$table->boolean('ifShow')->nullable()->default(0)->comment('消息是否对用户显示过');
			$table->boolean('ifRead')->nullable()->default(0)->comment('用户是否读过消息');
			$table->boolean('type')->nullable()->default(0)->comment('1提现通知，2，3，4, 10全部通知,20为游戏的操作');
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
		Schema::drop('qt_system_messages');
	}

}
