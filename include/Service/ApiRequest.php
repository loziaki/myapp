<?php
namespace Service;

use Service\Logger\LoggerFactory;

class ApiRequest extends Request
{
    public static function send($options, $raw2json = true)
    {
        $res = parent::send($options, false);
        $req = \json_encode($options, JSON_UNESCAPED_UNICODE);
        $log = sprintf('%s - %s - [%s]%s ', date('H:i:s'), $req, $res['status'], $res['body']);
        $logName = 'req_'.date('Y_W');
        LoggerFactory::get('file')->saveLog($log, $logName);

        if ($raw2json === true) {
            $res['body'] = json_decode($res['body'], true);
        }
        return $res;
    }
}
