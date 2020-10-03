<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQtSystemMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qt_system_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('uid');
            $table->string('title',255)
                    ->nullable();   

            $table->string('content',500);
            
            $table->tinyInteger('ifShow')
                    ->comment('消息是否对用户显示过')
                    ->nullable()
                    ->default(0);
            $table->tinyInteger('ifRead')
                    ->comment('用户是否读过消息')
                    ->nullable()
                    ->default(0);
            $table->tinyInteger('type')
                    ->comment('1提现通知，2，3，4, 10全部通知,20为游戏的操作')
                    ->nullable()
                    ->default(0);

            $table->engine = 'InnoDB'; 
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
        Schema::dropIfExists('qt_system_messages');
    }
}
