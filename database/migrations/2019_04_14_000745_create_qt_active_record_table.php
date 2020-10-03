<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQtActiveRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qt_active_record', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('manager_id');
			$table->string('path', 200);
			$table->string('method', 50);
			$table->string('ip', 50)->nullable();
			$table->string('input', 800)->nullable();
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
		Schema::drop('qt_active_record');
	}

}
