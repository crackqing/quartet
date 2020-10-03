<?php
namespace App\Traits;

class Csv
{

    /**
     * [csvFile]
     * @param  [Array] $head     [一维数据定义]
     * @param  [Int] $limit    [条件，自定缓存数据清空]
     * @param  [string] $filename [文件名自定义]
     * @return [csv file]           [description]
     */
    public static function csvFile($head, $limit = 20000, $filename ='csv格式文件', $listArr = [])
    {
        set_time_limit(0);  //百万以上的实现

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.csv"');
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output','a');
        //if (ob_get_level() == 0) ob_start();
        if (is_array($head)) {
            foreach ($head as $k => $v) {
                $head[$k] = iconv('utf-8','gbk',$v);
            }
        }else{
            throw new \Exception("{$head} is no array,please check!", 1);

        }

        fputcsv($fp, $head);

        $cnt = 0;   $count = count($listArr);

        for ($i=0; $i < $count; $i++) {
            $cnt++;
            if ($limit == $cnt) {
                ob_flush();
                flush();
                $cnt = 0;
            }

            $row = $listArr[$i];

            foreach ($row as $k => $v) {
                if (!is_numeric($v)) { 
                    $row[$k] = iconv('utf-8','gbk//IGNORE',$v);
                }else{
                    //FIX: execl 超过15位的数字后面为0问题 f
                    $row[$k] =   $v . "\t";
                }
            }


            fputcsv($fp,$row);

        }
    }
    /**
     * [csvFile]
     * @param  [Array] $head     [一维数据定义]
     * @param  [Int] $limit    [条件，自定缓存数据清空]
     * @param  [string] $filename [自定义文件路径，输出到指定目录。如果文件过久可以用cli的文件来访问]
     * @return [csv file]           [description]
     */
    public static function csvContent($head, $limit = 20000, $filename ='', $listArr = [])
    {
        set_time_limit(0);  //百万以上的实现

        $fp = fopen($filename,'w');

        if (is_array($head)) {
            foreach ($head as $k => $v) {
                $head[$k] = iconv('utf-8','gbk',$v);
            }
        }else{
            throw new \Exception("{$head} is no array,please check!", 1);

        }

        fputcsv($fp, $head);

        $cnt = 0;   $count = count($listArr);

        for ($i=0; $i < $count; $i++) {
            $cnt++;
            if ($limit == $cnt) {
                ob_flush();
                flush();
                $cnt = 0;
            }

            $row = $listArr[$i];

            foreach ($row as $k => $v) {
                if (!is_numeric($v)) { 
                   $row[$k] = iconv('utf-8','gbk//TRANSLIT//IGNORE',$v);
                }else{
                    //FIX: execl 超过15位的数字后面为0问题 f
                    $row[$k] = $v . "\t";
                }
            }

            fputcsv($fp,$row);

        }
    }

    /**
     * [csvFile]
     * @param  [Array] $head     [一维数据定义]
     * @param  [Int] $limit    [条件，自定缓存数据清空]
     * @param  [string] $filename [文件名自定义]
     * @return [csv file]           [description]
     */
    public static function csvFile2($head, $limit = 20000, $filename ='csv格式文件', $listArr = [])
    {
        set_time_limit(0);  //百万以上的实现

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.csv"');
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output','a');

        if (is_array($head)) {
            foreach ($head as $k => $v) {
                $head[$k] = iconv('utf-8','gbk',$v);
            }
        }else{
            throw new \Exception("{$head} is no array,please check!", 1);

        }

        fputcsv($fp, $head);

        $cnt = 0;   $count = count($listArr);

        for ($i=0; $i < $count; $i++) {
            $cnt++;
            if ($limit == $cnt) {
                ob_flush();
                flush();
                $cnt = 0;
            }

            $row = $listArr[$i];

            foreach ($row as $k => $v) {

                if (!is_numeric($v)) { 
                    $row[$k] = iconv('utf-8','gbk//IGNORE',$v);
                }else{
                    //FIX: execl 超过15位的数字后面为0问题 f
                    $row[$k] =  (int) $v;
                }
            }


            fputcsv($fp,$row);

        }
    }

    /**
     * 读取CSV文件
     * @param string $csv_file csv文件路径
     * @param int $lines       读取行数
     * @param int $offset      起始行数
     * @return array|bool
     */
    public static function readCsvLines($csv_file = '', $lines = 0, $offset = 0)
    {
        if (!$fp = fopen($csv_file, 'r')) {
            return false;
        }
        $i = $j = 0;
        //起始行数，offset 偏移
        while (false !== ($line = fgets($fp))) {
            if ($i++ < $offset) {
                continue;
            }
            break;
        }
        $data = [];
        // while (($j++ < $lines) && !feof($fp)) {
        //     $data[] = fgetcsv($fp);
        // }
        while (!feof($fp)) {
            $data[] = fgetcsv($fp);
        // }
        fclose($fp);
        return $data;
        }
        // $goods_list = [];
        // $file = fopen($csv_file,'r'); 
        // while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
        // //print_r($data); //此为一个数组，要获得每一个数据，访问数组下标即可
        //     $goods_list[] = $data;
        //  }
        //  return $goods_list;

    }

}