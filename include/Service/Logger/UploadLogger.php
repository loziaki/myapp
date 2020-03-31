<?php
namespace Service\Logger;

use Framework\MyApp;
use PDO;

class UploadLogger implements LoggerInterface
{
    private $msg = [];

    public function saveLog($message): bool
    {
        $this->msg[] = [
            'fid' => $message['fid'],
            'code' => $message['code'],
            'log' => $message['log']
        ];
        return true;
    }

    public function getLog($fid): array
    {
        $result = [];
        foreach ($this->msg as $v) {
            if ($v['fid'] == $fid) {
                $result[] = [
                    'code' => $v['code'],
                    'msg' => $v['log'],
                ];
            }
        }

        return $result;
    }
}