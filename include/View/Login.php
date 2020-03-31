<?php
namespace View;

use Service\Weixin;
use \Model\WxUser;

class Login extends \Framework\BaseView
{
    public function validate($request)
    {
        $request->request->getInt('code');
        if (empty($code)) {
            throw new \Framework\View\ParamException('缺少code参数');
        }

        $encryptedData = $request->request->get('encryptedData');
        $iv = $request->request->request->get('iv');

        if (empty($encryptedData) || empty($iv)) {
            throw new \Framework\View\ParamException('缺少用户授权参数');
        }

        return [
            'code' => $code,
            'encryptedData' => $encryptedData,
            'iv' => $iv
        ];
    }

    public function handlePost($env, $req)
    {
        $wxsdk = new Wexin(WEIXIN_APP_ID, WEIXIN_APP_SECRET);
        $wxres = $wxsdk>getAuthData($req['code']);
        // $wxres['session_key'] = '';
        $userInfo = $wxsdk->decryptData($req['encryptedData'], $req['iv'], $wxres['session_key']);

        //saveUser
        $result = WxUser::select()->field('id')->where('openId', $userInfo['openId'])->find();
        if ($result == false) {
            $uid = (new WxUser($userInfo))->insert();
        } else {
            (new WxUser($userInfo))->update('uid', $uid);
        }

        $token  = $this->getToken();

        //saveToken
        (new \Model\TokenInfo([
            'openId' => $userInfo['openId'],
            'session_key' => $wxres['session_key'],
            'token'=> $token,
        ]))->replace('uid', $uid);

        return $this->success(['token'=>$token]);
    }

    private function getToken($len = 16)
    {
        global $options;
        $defaultStr = $options['APP_NAME'];

        $token  = null;
        $randStr  = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for ($i=0; $i<$len; $i++) {
            $randStr .= $strPol[rand(0, $max)];
        }

        $token = md5(sha1($randStr) . $defaultStr);
        return $token;
    }
}
