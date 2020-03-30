<?php
namespace Controller;

use Framework\MyApp;
use Service\Util;
use \PDO;
use \Exception;

class CliController
{
    public function refreshAccessTokenAction()
    {
        try {
            //首先是获取token
            list($accessToken,$expireIn) = array_values(WxController::getAccessToken());
            //然后保存token
            self::storeAccessToken($accessToken,time()+$expireIn);
            return 'OK';
        } catch (Exception $e) {
            return 'FAIL:'.$e->getMessage();
        }
    }

    public static function storeAccessToken(string $token,int $expire)
    {
        $db = Util::getMySQLInstrance();
        $db->beginTransaction();
        $sql = 'REPLACE INTO global_var(name,val,expire_time,update_time) values(:name,:val,:expireTime,:updateTime);';
        $stat = $db->prepare($sql);
        $stat->execute([
            'name' => 'wx-access-token',
            'val' => $token,
            'expireTime' => date('Y-m-d H:i:s',$expire),
            'updateTime' => date('Y-m-d H:i:s'),
        ]);
        $db->commit();
    }
}