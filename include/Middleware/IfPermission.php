<?php
namespace Middleware;

use Model\Auth;
use Framework\Middleware\NotFineException;

class IfPermission extends \Framework\Middleware
{
    const CODE_DENIED = -40004;

    private $permissions = [];
    /**
     * $pArr array
     */
    public function __construct($pArr)
    {
        $this->permissions = $pArr;
    }

    public function handle(&$appParams, $request)
    {
        if (!array_key_exists('uid', $appParams)) {
            //如果没检查token，就先检查token
            (new GetCurrentUser())->handle($appParams, $request);
        }

        //如果这里uid还是违规了
        if (empty($appParams['uid'])) {
            throw new NotFineException('请重新登陆');
        }

        $pArr = Auth::getPermission($appParams['uid']);

        if (!array_intersect($this->permissions, $pArr)) {
            throw new NotFineException('没有权限进行操作', self::CODE_DENIED);
        }

        return;
    }
}
