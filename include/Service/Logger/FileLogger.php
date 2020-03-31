<?php
namespace Service\Logger;

use Framework\MyApp;
use PDO;
use Exception;

class FileLogger implements LoggerInterface
{
    private $log_storage_path;

    const STORAGE_DIR = '/log/';

    public function __construct()
    {
        $this->log_storage_path = $this->setStorageDir();
    }

    public function saveLog($message, $filename = ''): bool
    {
        if (defined('ENV') != 'master') {
            return true;
        }

        if (!is_string($message)) {
            throw new Exception('"Message" is not a string');
        }

        $filePath = $this->getLogFilePath($filename);

        $fp = fopen($filePath, 'a+');
        if ($fp != false) {
            fwrite($fp, $message.PHP_EOL);
        } else {
            return false;
        }
        fclose($fp);
        return true;
    }


    private function getLogFilePath($filename)
    {
        $filename = (empty($filename))? $filename :  date('Y_W');
        return $this->log_storage_path.$filename;
    }

    private function setStorageDir()
    {
        if (!is_dir(TEMP_DIR)) {
            mkdir(TEMP_DIR);
        }

        if (!is_dir(TEMP_DIR.self::STORAGE_DIR)) {
            mkdir(TEMP_DIR.self::STORAGE_DIR);
        }

        return TEMP_DIR.self::STORAGE_DIR;
    }
}
