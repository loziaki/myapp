<?php
namespace View;

use Service\Weixin;
use \Model\WxUser;

class Login extends \Framework\BaseView
{
    public function validate($request)
    {
        $code  = $request->request->get('code');
        if (empty($code)) {
            throw new \Framework\View\ParamException('缺少code参数');
        }

        $encryptedData = $request->request->get('encryptedData');
        $iv = $request->request->get('iv');

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
        $wxsdk = new \Service\Wxsdk\Weixin(WEIXIN_APP_ID, WEIXIN_APP_SECRET);
        try {
            // $wxres = $wxsdk->getAuthData($req['code']);
            // $userInfo = $wxsdk->decryptData($req['encryptedData'], $req['iv'], $wxres['session_key']);
        } catch (\Service\Wxsdk\WxsdkException $e) {
            return $this->error($e->getMessage());
        }
        // \file_put_contents(TEMP_DIR.'userinfo', \serialize($userInfo));
        $userInfo = \unserialize(\file_get_contents(TEMP_DIR.'userinfo'));
        //saveUser
        $result = WxUser::select()->field('uid')->where('openId', $userInfo['openId'])->find();
        if ($result == false) {
            $uid = (new WxUser($userInfo))->save();
        } else {
            (new WxUser($userInfo))->update('uid', $result['uid']);
        }

        $token  = $this->getToken();

        //saveToken
        (new \Model\TokenInfo([
            'openId' => $userInfo['openId'],
            // 'session_key' => $wxres['session_key'],
            'session_key' => 'test',
            'token'=> $token,
        ]))->save('uid', $result['uid']);

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
