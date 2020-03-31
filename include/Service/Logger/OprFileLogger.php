<?php
namespace Service\Logger;

use Framework\MyApp;
use PDO;
use Exception;

class OprFileLogger extends FileLogger
{
    const PRESET_NAME = 'filelog_param';
    const DEFAULT_DATA = [
        'time' => '',
        'uid' => '',
        'requestID' => '',
        'path' => '',
        'url' => '',
        'body' => '',
    ];
    
    private $datas;
    
    public function __construct(array $param)
    {
        parent::__construct();
        $this->datas = $this->setParam($param);
        $this->datas['requestID'] = $this->get_request_id();
    }

    public function start()
    {
        $this->savelog($this->toString($this->datas));

    }

    public function success(string $message = null)
    {
        $this->saveLog($this->toString([
            $this->datas['time'],
            $this->datas['uid'],
            $this->datas['requestID'],
            $this->datas['path'],
            '#success#'.$message
        ]));
    }

    public function error(string $message = null)
    {
        $this->saveLog($this->toString([
            $this->datas['time'],
            $this->datas['uid'],
            $this->datas['requestID'],
            $this->datas['path'],
            '#error#'.$message
        ]));
    }

    private function setParam($param)
    {
        $request = array_intersect_key($param, self::DEFAULT_DATA);
        $datas = array_merge(self::DEFAULT_DATA, $request);

        return $datas;
    }

    private function toString($param): string
    {
        $str = implode(' - ',$param);
        return $str;
    }

    private function get_request_id(): string
    {
        $random = substr(strval(rand(10000,19999)),1,4);

        return $random;
    }
}