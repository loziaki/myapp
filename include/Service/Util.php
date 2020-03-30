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

    public static function getGlobalVar($name)
    {
        try {
            $db = self::getMySQLInstrance();
            $db->beginTransaction();
            $sql = 'SELECT val,expire_time FROM global_var WHERE name = ? FOR UPDATE';
            $stat = $db->prepare($sql);
            $stat->execute([$name]);
            $db->commit();
            return $stat->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('QwQ MySQL CONNECTING ERROR: '. $e->getMessage());
        }
    }
}
