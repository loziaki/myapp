<?php
namespace Service\Wxsdk;

use Service\ApiRequest;

class Weixin
{
    private $appid;
    private $appSecret;

    public function __construct($appid, $appSecret)
    {
        $this->appid = $appid;
        $this->appSecret = $appSecret;
    }

    /**
     * 微信小程序登陆授权获取用户openid等信息
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/login/auth.code2Session.html
     * @param $code
     * @return array
     * @throws Exception
     */
    public function getAuthData($code)
    {
        $requestParams = [
            'appid' => $this->appid,
            'secret' => $this->appSecret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];

        list($status, $body) = array_values(ApiRequest::get([
            'url' => 'https://api.weixin.qq.com/sns/jscode2session?' . http_build_query($requestParams),
            'timeout' => 3000
        ]));

        if ($status !== 200 || !$body) {
            throw new WxsdkException('no reply receive from weixin');
        }

        if (isset($body['errcode'])) {
            throw new WxsdkException('['.$body['errcode'].']'.$body['errmsg']);
        }

        return $body;
    }


    /**
     * 后台校验与解密开放数据
     * @param $encryptedData
     * @param $iv
     * @param $session_key
     * @return mixed
     * @throws Exception
     */
    public function decryptData($encryptedData, $iv, $session_key)
    {
        $result='';
        $wxBizDataCrypt = new DataCrypt($this->appid, $session_key);
        $errCode = $wxBizDataCrypt->decryptData($encryptedData, $iv, $result);
        if ($errCode != 0) {
            throw new WxsdkException('解密失败,请检查参数是否正确');
        }

        return json_decode($result, true);
    }

    /**
     * 从微信服务器获取access_token
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/access-token/auth.getAccessToken.html
     * @return mixed
     * @throws Exception
     */
    public function getAccessToken()
    {
        $requestParams = [
            'appid' => $this->appid,
            'secret' => $this->appSecret,
            'grant_type' => 'client_credential'
        ];

        list($status, $body) = array_values(ApiRequest::get([
            'url' => 'https://api.weixin.qq.com/cgi-bin/token?' . http_build_query($requestParams),
            'timeout' => 3000
        ]));

        if ($status !== 200 || !$body) {
            throw new WxsdkException('no reply receive from weixin');
        }

        if (isset($body['errcode'])) {
            throw new WxsdkException('['.$body['errcode'].']'.$body['errmsg']);
        }

        return $body;
    }

    /**
     * 从微信服务器获取一个分享用的二维码（无限量的接口）
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html
     */
    public function getQrCodeTypeB($accessToken, $options)
    {
        if (!isset($options['scene']) || !isset($options['page'])) {
            throw new WxsdkException('miss params sence or page');
        }

        list($status, $body) = array_values(ApiRequest::jsonPost([
            'url' => 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $accessToken,
            'timeout' => 3000,
            'data' => $options
        ]));

        if ($status !== 200 || !$body) {
            throw new WxsdkException('no reply receive from weixin');
        }

        if (isset($body['errcode'])) {
            throw new WxsdkException('['.$body['errcode'].']'.$body['errmsg']);
        }

        $qrImage = imagecreatefromstring($body);
        if ($qrImage == false) {
            throw new WxsdkException('create qrcode image fail');
        }
        return $body;
    }
}
