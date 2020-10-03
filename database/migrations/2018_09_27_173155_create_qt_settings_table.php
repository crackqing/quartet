<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQtSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if (!Schema::hasTable('qt_settings')){
            Schema::create('qt_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('key');
                $table->string('value');
                $table->tinyInteger('type');
            });            
        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qt_settings');
    }
}
