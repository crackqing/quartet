<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_menus', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('pid')->unsigned()->nullable()->default(0)->comment('默认为零，顶级菜单');
			$table->string('name', 100)->nullable()->comment('菜单名称');
			$table->string('ico', 100)->nullable()->comment('菜单图标');
			$table->string('url', 100)->nullable()->comment('vue前端的路由地址');
			$table->integer('sort')->nullable()->default(0)->comment('菜单的排序默认为1');
			$table->string('permission')->nullable()->comment('后端的权限标识 ');
			$table->boolean('show')->nullable()->comment('0默认显示 ,1为不显示,单个按钮的权限设置');
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
		Schema::drop('qt_menus');
	}

}
