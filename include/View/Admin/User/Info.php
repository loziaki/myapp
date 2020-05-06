<?php
namespace View\Admin\User;

use Model\Admin\User;

class Info extends \View\Admin\AdminBaseView
{
    public function __construct()
    {
        $this->middlewares = [
            new \Middleware\AdminTokenCheck()
        ];
    }

    public function validate($request)
    {
        $token  = $request->query->get('token');
        if (empty($token)) {
            throw new \Framework\View\ParamException('请传token', self::ERR_NOTICE_USER);
        }

        return [
            'token' => $token
        ];
    }

    public function handleGet($env, $req)
    {
        if ($env['token'] != $req['token']) {
            return $this->error('非法token', self::ERR_ILLEGAL_TOKEN);
        }

        $param= [
            'roles' => ($env['username'] == 'admin')? ['admin'] : ['staff'],
            'introduction' => 'wwwww',
            'avatar' => 'http://'.\Service\Util::getApiServerPath().'/public/1.gif',
            'name' => $env['username']
        ];
        return $this->success(['data' => $param], self::SUCESS_CODE);
    }
}
