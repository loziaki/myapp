<?php
namespace Middleware;

use \Framework\MiddlewareInterface;
use \Service\JwtMaker;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use \PDO;
use Framework\MyApp;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class TokenCheck implements MiddlewareInterface
{
    const JWT_HEADER = 'mytoken';

    public function handle(): bool
    {
        $tokenStr = MyApp::$request->headers->get(self::JWT_HEADER);
        if (empty($tokenStr)) {
            MyApp::setResponse(100,'invalid token');
            return false;
        }
        $token = (new Parser())->parse((string) $tokenStr);
        //从header获得对应的uid
        $uid = $token->getHeader('jti');
        MyApp::$preset->set('uid',$uid);
        //判断签名是否签名正确的
        $signer = new Sha256();
        if (false == $token->verify($signer,JwtMaker::$key)) {
            MyApp::setResponse(101,'invalid token');
            return false;
        }
        //如果有，那么就判断当前时戳是不是过期了
        //如果过期了就false
        // if (true == $token->isExpired()) {
        //     MyApp::setResponse(103,'invalid token');
        //     return false;
        // }

        if (false == $this->checkValidating($token,$uid)) {
            MyApp::setResponse(102,'invalid token');
            return false;
        }
        
        return true;
    }

    private function checkValidating($token,$id): bool
    {
        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer(JwtMaker::$issuer);
        $data->setAudience(JwtMaker::$audience);
        $data->setId($id);
        
        return $token->validate($data);
    }
}