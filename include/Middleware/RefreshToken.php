<?php
namespace Middleware;

use Framework\MyApp;
use Service\JwtMaker;
use Controller\LoginController;
use Lcobucci\JWT\Parser;
use Framework\MiddlewareInterface;
use Model\User;
use Exception;

class RefreshToken implements MiddlewareInterface
{
    public function handle(): bool
    {
        $uid = MyApp::$preset->get('uid');
        $tokenStr= MyApp::$request->cookies->get(LoginController::COOKIE_TOKEN,null);
        $name= MyApp::$request->cookies->get(LoginController::COOKIE_UNAME,null);
        $remoteAddr = Myapp::$request->server->get('REMOTE_ADDR');

        if(empty($tokenStr)){
            return false;
        }

        $token=(new Parser())->parse((string)$tokenStr);

        try{
            $randNum = JwtMaker::getRandNum();
            (new User([
                'uid' => $uid,
                'remoteAddr' => $remoteAddr,
                'serial' => $randNum
            ]))->updateLoginInfoByUid();

            //生成Jwt
            $jwtStr = (new JwtMaker())
            ->setPeriod(time(),LoginController::JWT_AFFECT_PERIOD)
            ->createJwt($uid,$randNum);
            //设置cookie
            setcookie(LoginController::COOKIE_UNAME,$name,time()+LoginController::JWT_AFFECT_PERIOD,API_PATH,API_DOMAIN);
            setcookie(LoginController::COOKIE_TOKEN,$jwtStr,time()+LoginController::JWT_AFFECT_PERIOD,API_PATH,API_DOMAIN);
        }catch (\Exception $e){
            MyApp::setResponse($e->getCode(),$e->getMessage());
            return false;
        }
        return true;
    }
}