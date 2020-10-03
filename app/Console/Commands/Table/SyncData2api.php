<?php

namespace App\Console\Commands\Table;

use Illuminate\Console\Command;

use App\Service\QpApi;
use App\Models\Game\RecordDetail;

class SyncData2api extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:table';

    protected $api;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(QpApi $api)
    {
        parent::__construct();

        $this->api = $api;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        #一份内，同步三次就行. 每次2000条
        for ($i=0; $i <= 5; $i++) { 

            // 同步玩家记录
            $id = RecordDetail::orderBy('id', 'desc')->limit(1)->value('id');
            $content = $this->api->getGameRecordDetail($id ?? 1);
            if ($content) {
                foreach ($content as $v) {
                    RecordDetail::create($v);
                }
            }
            sleep(3);
        }

    }
}
