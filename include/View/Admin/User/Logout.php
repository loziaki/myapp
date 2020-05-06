<?php
namespace View\Admin\User;

use Model\Admin\User;

class Logout extends \View\Admin\AdminBaseView
{
    public function __construct()
    {
        $this->middlewares = [
            new \Middleware\AdminTokenCheck()
        ];
    }

    public function handleGet($env, $req)
    {
        $param = [
            'toekn' => $this->getToken(),
        ];

        (new User($param))->save('uid', $env['uid']);
        return $this->success(['uid' => $env['uid']], self::SUCESS_CODE);
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
