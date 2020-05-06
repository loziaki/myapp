<?php
namespace View;

use Service\Weixin;
use \Model\WxUser;
use \Model\TokenInfo;
use \Model\User;
use \Model\Auth;

use Symfony\Component\HttpFoundation\Cookie;

class Login extends \Framework\BaseView
{
    const TOKEN_NAME = 'uutoken';

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
            $wxres = $wxsdk->getAuthData($req['code']);
            $wxUserInfo = $wxsdk->decryptData($req['encryptedData'], $req['iv'], $wxres['session_key']);
        } catch (\Service\Wxsdk\WxsdkException $e) {
            return $this->error($e->getMessage());
        }
        // \file_put_contents(TEMP_DIR.'userinfo', \serialize($userInfo));
        // $wxUserInfo = \unserialize(\file_get_contents(TEMP_DIR.'userinfo2'));
        // $wxres = ['session_key' => 'test2'];
        //saveUser
        $result = WxUser::select()->field('uid')->where('openId', $wxUserInfo['openId'])->find();
        if ($result == false) {
            //先防止不能读取权限缓存导致创建了用户
            $pArr = Auth::getDefaultPermissions();

            $uid = (new WxUser($wxUserInfo))->save();
            (new Auth([
                'permission' => $pArr
            ]))->save($uid);
        } else {
            $uid = $result['uid'];
            (new WxUser($wxUserInfo))->update('uid', $result['uid']);
        }

        $userInfo = User::select()->field(['uid','uidKey'])->where('uid', $uid)->find();
        if ($userInfo == false) {
            $userInfo = (new User([
                'uid' => $uid,
                'gender' => $wxUserInfo['gender'],
                'role' => User::ROLE_GUEST
            ]))->save();
        }

        $token  = $this->getToken();

        //saveToken
        (new \Model\TokenInfo([
            'openId' => $wxUserInfo['openId'],
            'session_key' => $wxres['session_key'],
            // 'session_key' => 'test2',
            'token'=> $token,
        ]))->save('uid', $uid);

        $res = $this->success(['token'=>$token, 'uid'=>intval($uid)]);
        return $res;
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
