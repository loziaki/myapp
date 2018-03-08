<?php
namespace Service\Logger;

class MsgLogger implements LoggerInterface
{
    public function saveLog($log)
    {
        if (!is_dir(TEMP_DIR)) {
            mkdir(TEMP_DIR);
        }

        $fileName = TEMP_DIR.'/msg'.date('Ymd');
        $timestamp = '['.date('Y-m-d H:i:s').']';
        $log = $timestamp.$log['machine'].' - '.$log['msg'].PHP_EOL;
        $fp = fopen($fileName,'a');
        fwrite($fp,$log);
        fclose($fp);
    }
}