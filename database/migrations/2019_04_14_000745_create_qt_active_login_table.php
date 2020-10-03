<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtActiveLoginTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_active_login', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('email', 100)->default('0')->comment('登录的帐号');
			$table->string('password', 100)->default('0')->comment('登录错误显示密码，正确显示***');
			$table->string('captcha', 100)->default('0')->comment('显示生成的验证码与输入的验证码');
			$table->boolean('type')->default(0)->comment('0为用户,1为BOSS');
			$table->string('ip')->default('0');
			$table->string('ua', 1000)->default('0');
			$table->boolean('status')->default(0)->comment('0为正常，1为冻结，-1为封号状态');
			$table->integer('user_id')->nullable();
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
		Schema::drop('qt_active_login');
	}

}
