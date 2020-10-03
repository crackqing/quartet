<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSfDailyChoushuiUidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_daily_choushui_uid', function (Blueprint $table) {
            $table->increments('id');
            $table->double('total_choushui',15,2)->comment('统计昨天的抽水,默认只能运行一次.'); 
            $table->string('time_rand', 100)->comment('时间的区间记录');
            $table->integer('uid')->comment('统计UID'); 
            $table->unsignedMediumInteger('is_dealer')->comment('是否为币商'); 
            $table->integer('performance')->comment('返利的倍数 * 对应的数值 '); 
            $table->integer('multiple')->comment('返利的倍数'); 
            $table->integer('return_gold')->comment('返利的金钱'); 
            $table->integer('bind_id')->comment('对应绑定ID'); 
            $table->date('time');

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sf_daily_choushui_uid');
    }
}
