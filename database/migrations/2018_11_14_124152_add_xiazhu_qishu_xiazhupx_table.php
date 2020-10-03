<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddXiazhuQishuXiazhupxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('sf_game_record_detail','xiazhu')
           && !Schema::hasColumn('sf_game_record_detail','qishu')
           && !Schema::hasColumn('sf_game_record_detail','xiazhupx')
           && !Schema::hasColumn('sf_game_record_detail','kaijiangpx')
            )  {
            Schema::table('sf_game_record_detail', function (Blueprint $table) {
                $table->string('xiazhu',255)->nullable()->comment('下注类型');
                $table->string('qishu',255)->nullable()->comment('当前期数');
                $table->string('xiazhupx',255)->nullable()->comment('下注牌型');
                $table->string('kaijiangpx',255)->nullable()->comment('开奖牌型');
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

    }
}
