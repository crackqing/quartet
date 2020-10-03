<?php

namespace App\Console\Commands\Backup;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Carbon;
use Alchemy\Zippy\Zippy;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    protected $fileName ;


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            //        $date = date('Y-m-d-H-i-s');
            //$this->fileName = base_path('backups/backup_'.$date.'.sql');
            // 删除N天之前的备份,本地与网盘各存一份，保证数据安全. 打包zip
            // $directory = base_path('backups/');
            // $files     = scandir($directory);
            
            // foreach ($files as $file) {
            //     // dump(Carbon::now()->diffIndays(substr($file, 7, 10)),$file) ;   
            //     if (!in_array($file, ['.', '..'])
            //             && Carbon::now()->diffIndays(substr($file, 7, 10))
            //             >= env('DB_BACKUP_DAYS', 5)) {
                    
            //         unlink($directory . $file);
            //     }
            // }
            #每天5点定时备份所有线上的数据库,防止被黑或者DB服务出问题。等等
            $backupDb = [
                'df_db',
                'tiantian_db',
                'jg_db',
                'paysdk_db',
                'hero_db'
            ];
            foreach($backupDb as $v){
                $date = date('Y-m-d').'_'.$v;

                $this->fileName = base_path('backups/backup_'.$date.'.sql');
                //--single-transaction
                $this->process = new Process(sprintf(
                    'mysqldump -h %s -u%s -p%s -q %s   > %s',
                    config('database.connections.'.$v.'.host'),
                    config('database.connections.'.$v.'.username'),
                    config('database.connections.'.$v.'.password'),
                    // config('database.connections.'.$v.'.database'),
                    '--all-databases',
                    $this->fileName
                ),null,null,null,3600);
    
                $this->process->mustRun();
            }
            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has been failed.');
        }
    }
}
