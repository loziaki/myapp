<?php
namespace Service;

use \PDO;

class Util
{
    public static $db;
    public static function & getMySQLInstrance()
    {
        //初始化数据库连接
        try {
            if (!isset(self::$db)) {
                $dsn = 'mysql:host='.DB_LOCALOHOTS.';port='.DB_PORT.';dbname='.DB_DATABASE.';charset='.DB_CHARSET;
                $db = new PDO($dsn, DB_USER, DB_PASSWORD);
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$db = $db;
            }
            return self::$db;
        } catch (PDOException $e) {
            throw new Exception('QwQ MySQL CONNECTING ERROR: '. $e->getMessage());
        }
    }

    private static function globalVarPath($name)
    {
        return  TEMP_DIR.'var/'.$name;
    }

    public static function getGlobalVar($name)
    {
        $cPath = self::globalVarPath($name);
        if (!file_exists($cPath)) {
            return false;
        }
        $content =  file_get_contents($cPath);
        return unserialize($content);
    }

    public static function setGlobalVar($name, $value)
    {
        $content = \serialize($value);
        $cPath = self::globalVarPath($name);

        $dir = dirname($cPath);
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        file_put_contents($cPath, $content);
        return;
    }

    public static function getIp()
    {
        $unknown = 'unknown';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && $_SERVER['HTTP_X_FORWARDED_FOR']
        && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])
        && $_SERVER['REMOTE_ADDR']
        && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = $unknown;
        }
        // 处理多层代理的情况
        if (false !== strpos($ip, ',')) {
            $ip = reset(explode(',', $ip));
        }
        return $ip;
    }

    public static function getApiServerPath()
    {
        $url = '';
        if (defined('API_DOMAIN') && defined('API_PATH')) {
            $url = API_DOMAIN.API_PATH;
            if (preg_match('/\/$/', $url)) {
                $url = substr($url, 0, -1);
            }
        }
        return $url;
    }
}
