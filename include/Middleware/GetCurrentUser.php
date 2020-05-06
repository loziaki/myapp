<?php
namespace Middleware;

use Model\TokenInfo;
use Framework\Middleware\NotFineException;

class GetCurrentUser extends \Framework\Middleware
{
    public function handle(&$appParams, $request)
    {
        $token = $request->headers->get(\View\Login::TOKEN_NAME);

        if (!empty($token)) {
            $result = TokenInfo::select()->field('uid')->where([
                ['token', $token],
                ['token_expire', '>', time()]
            ])->find();
            if (!isset($result['uid'])) {
                //错误了都不清除错误的cookie，留着有可能去处理
                throw new NotFineException('请重新登陆');
            }

            $appParams['uid'] = $result['uid'];
            return;
        }

        throw new NotFineException('请先登陆');
        return;
    }
}
