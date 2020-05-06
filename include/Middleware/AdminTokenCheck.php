<?php
namespace Middleware;

use Model\Admin\User;
use Framework\Middleware\NotFineException;

class AdminTokenCheck extends \Framework\Middleware
{
    public function handle(&$appParams, $request)
    {
        $token = $request->headers->get(\View\Admin\AdminBaseView::TOKEN_NAME);

        if (!empty($token)) {
            $result = User::select()->field(['uid','username','token_expire'])->where([
                ['token', $token]
            ])->find();
            if (!isset($result['uid'])) {
                throw new NotFineException('请重新登陆', \View\Admin\AdminBaseView::ERR_OTHER_CLIENTS_LOGGED_IN);
            }

            if ($result['token_expire'] < time()) {
                throw new NotFineException('请重新登陆', \View\Admin\AdminBaseView::ERR_TOKEN_EXPIRED);
            }

            $appParams['uid'] = $result['uid'];
            $appParams['username'] = $result['username'];
            $appParams['token'] = $token;
        } else {
            throw new NotFineException('请先登陆', \View\Admin\AdminBaseView::ERR_ILLEGAL_TOKEN);
        }
        return;
    }
}
