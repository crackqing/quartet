<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * Class CsvController 文档导出接口
 * @package App\Http\Controllers\Api
 */
class CsvController extends BaseController
{
    /**
     * 数据库表导出 function
     *
     * @return string
     */
    public function csvExport(Request $request)
    {
        $table = $request->table ?? false;
        $timeRange = $request->timeRange ?? false;
        
        if ($table) {
            $db = \DB::table($table)
                    ->when($timeRange,function($query) use ($timeRange){
                        return $query->whereBetween('created_at',[$timeRange[0],$timeRange[1]]);
                    })
                    ->get()
                    ->toArray();
            $header = [];
            $fileName = $table.$timeRange;
    
            Csv::csvFile($header,20000,$fileName,$db);
        }
        return $this->failed('failed');
    }
}
