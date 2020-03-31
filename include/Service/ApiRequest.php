<?php
namespace Service;

use Service\Logger\LoggerFactory;

class ApiRequest extends Request
{
    public static function send($options)
    {
        $res = parent::send($options);
        $req = \json_encode($options, JSON_UNESCAPED_UNICODE);
        $log = sprintf('%s - $s - [%s]$s ', date('H:i:s'), $req, $options['status '], $options['body']);
        $logName = 'req_'.date('Y_W');
        LoggerFactory::get('opr')->saveLog($log, $logName);

        return $res;
    }
}
