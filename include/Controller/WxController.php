<?php
namespace Controller;

use Framework\MyApp;
use Service\Request;
use Service\Util;
use \Exception;

class WxController
{
    public static function getQrCodeTypeB($options)
    {
        if (!isset($options['scene']) || !isset($options['page'])) {
            throw new Exception('miss params sence or page');
        }
        
        try {
            list($accessToken,$expire) = array_values(Util::getGlobalVar('wx-access-token'));
        } catch (PDOException $e) {
            throw new Exception('can not get the token param');
        }

        list($status, $body) = array_values(Request::jsonPost([
            'url' => 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $accessToken,
            'timeout' => 3000,
            'data' => $options
        ]));
        if ($status !== 200 || !$body || isset($body['errcode'])) {
            throw new Exception(json_encode($body));
        }
        $qrImage = imagecreatefromstring($body);
        if ($qrImage == false) {
            throw new Exception('create qrcode image fail');
        }
        return $body;
    }

    public static function getAccessToken()
    {
        $requestParams = [
            'appid' => WX_APP_ID,
            'secret' => WX_SECRET,
            'grant_type' => 'client_credential'
        ];

        list($status, $body) = array_values(Request::get([
            'url' => 'https://api.weixin.qq.com/cgi-bin/token?' . http_build_query($requestParams),
            'timeout' => 3000
        ]));

        if ($status !== 200 || !$body || isset($body['errcode'])) {
            throw new Exception(json_encode($body));
        }

        return [
            'access_token' => ($body['access_token'])?? null,
            'expireIn' => ($body['expires_in'])?? null,
        ];
    }

    public static function getSessionidAndOpenid($code)
    {
        $requestParams = [
            'appid' => WX_APP_ID,
            'secret' => WX_SECRET,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];

        list($status, $body) = array_values(Request::get([
            'url' => 'https://api.weixin.qq.com/sns/jscode2session?' . http_build_query($requestParams),
            'timeout' => 3000
        ]));

        if ($status !== 200 || !$body || isset($body['errcode'])) {
            throw new Exception(json_encode($body));
        }

        return [
            'timestamp' => (time())?? null,
            'sessionKey' => ($body['session_key'])?? null,
            'expireIn' => ($body['expires_in'])?? null,
            'openid' => ($body['openid'])?? null,
        ];
    }
}