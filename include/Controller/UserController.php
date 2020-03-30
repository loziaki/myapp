<?php
namespace Controller;

use Framework\MyApp;
use \PDO;
use \Model\User;
use \Model\UserGroup;
use \Model\CatPresent;
use Service\JwtMaker;
use Service\Util;
use \Exception;

class UserController
{
    public function loginAction()
    {
        //先获取code
        $code = Myapp::$preset->get('wx_code');
        $encryptedData = Myapp::$preset->get('wx_encryptedData');
        $iv = Myapp::$preset->get('wx_iv');

        try {
            list($timestamp,$sessionKey,$expireIn,$openid) =  array_values(WxController::getSessionidAndOpenid($code));
            $userInfo = UserController::encryptedUserData($encryptedData,$sessionKey,$iv);
            $uid = UserController::storeUserInfo($openid,$sessionKey,$userInfo);
            $token = (new JwtMaker())->setPeriod($timestamp)->createjwt($uid)->__toString();
        } catch (Exception $e) {
            MyApp::setResponse(0,$e->getMessage());
            return;
        }
        unset($userInfo['openId']);
        return json_encode([
            'userInfo' => $userInfo,
            'uid' => $uid,
            'token' => $token,
        ]);
    }

    public static function storeUserInfo($openid,$sessionKey,array $userinfo)
    {
        $userInfo = new User([
            'openid' => $userinfo['openId'],
            'nickName' => $userinfo['nickName'],
            'avatarUrl' => $userinfo['avatarUrl'],
            'gender' => intval($userinfo['gender']),
            'sessionKey' => $sessionKey,
            'city' => $userinfo['city'],
            'province' => $userinfo['province'],
            'country' => $userinfo['country'],
        ]);
        //判断openid对应的用户是否存在
        $uid = $userInfo->getUidByOpenid();
        if ($uid == 0) {
            //如果不存在，那么就创建用户
            $uid = $userInfo->Insert();
        } else {
            //刷新用户信息
            //如果存在那么就刷新sessionkey
            $userInfo->updateInfoByUid();
        }
        //这里判断一下今天是不是第一次抽奖
        return $uid;
    }

    public static function encryptedUserData($encryptedData,$sessionKey,$iv)
    {
        //解析得到用户信息
        $decryptData = \openssl_decrypt(
            base64_decode($encryptedData),
            'AES-128-CBC',
            base64_decode($sessionKey),
            OPENSSL_RAW_DATA,
            base64_decode($iv)
        );
        if ($decryptData == false) {
            throw new Exception('encrypte userinfo fail');
        }

        $userinfo = json_decode($decryptData,true);
        return $userinfo;
    }
}