<?php
namespace View\Admin\User;

use Model\Admin\User;

class Login extends \View\Admin\AdminBaseView
{
    public function validate($request)
    {
        $content = $request->getContent();
        $request = \json_decode($content, true);
        $username = ($request['username'])?? null;
        $password = ($request['password'])?? null;

        if (empty($username) || empty($password)) {
            throw new \Framework\View\ParamException('请填写登陆信息', self::ERR_NOTICE_USER);
        }

        return $request;
    }

    public function handlePost($env, $req)
    {
        $result = User::select()->field(['uid','username'])->where([
            ['username',$req['username']],
            ['password', User::encryptPassword($req['password'])]
        ])->find();

        if ($result == false) {
            return $this->error('密码错误', self::ERR_NOTICE_USER);
        }

        $param = [
            'token' => $this->getToken(),
        ];

        (new User($param))->save('uid', $result['uid']);
        return $this->success([
            'data' => $param
        ], self::SUCESS_CODE);
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
