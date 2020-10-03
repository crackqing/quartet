<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password');
			$table->string('remember_token', 100)->nullable();
			$table->timestamps();
			$table->integer('bind_id')->nullable()->default(0)->comment('绑定ID, 与email层级关系 默认为0');
			$table->string('mobile', 20)->nullable()->comment('手机号码 ');
			$table->integer('agent_id')->nullable()->comment('玩家ID');
			$table->string('agent_nickname', 100)->nullable()->comment('玩家姓名');
			$table->boolean('status')->nullable()->default(0)->comment('0默认正常-1为封号状态,1为冻结');
			$table->boolean('manager')->nullable()->default(0)->comment('0为默认的帐号,1为管理帐号能登录后台.');
			$table->integer('amount')->nullable()->default(0)->comment('额度，为可赠送的数值');
			$table->string('remark', 100)->nullable()->comment('当前帐号的备注说明');
			$table->string('ip', 100)->nullable()->comment('IP地址');
			$table->bigInteger('balance')->nullable()->comment('每小时的余额显示');
			$table->index(['email','bind_id','manager']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
