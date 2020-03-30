<?php
namespace Service\Logger;

use Model\ErrorMsg;

class MyDbLogger implements LoggerInterface
{
    public function saveLog($message)
    {
        (new ErrorMsg([
            'fid' => $message['fid'],
            'code' => $message['code'],
            'log' => $message['log']
        ]))->insert();
    }

    public function getLog($fid): array
    {
        $msgArr = (new ErrorMsg([
            'fid' => $fid,
        ]))->getMsg();
        return $msgArr;
    }
}