<?php
namespace Middleware;

use \Service\JwtMaker;
use \Controller\LoginController;
use \Framework\MiddlewareInterface;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Model\User;
use Framework\MyApp;

class TokenCheck implements MiddlewareInterface
{
    private $token;
    private $uid;

    public function handle(): bool
    {
        $tokenStr= MyApp::$request->cookies->get(LoginController::COOKIE_TOKEN,null);
        //是否登录:cookie检查
        if(empty($tokenStr)){
            MyApp::setResponse(2000,'用户未登录，请先登录');
            return FALSE;
        }
        $this->token = (new Parser())->parse((string)$tokenStr);
        $this->uid = $this->token->getHeader('jti');

        try{
            //密钥验证
            if (FALSE == $this->isverify()) {
                MyApp::setResponse(3001,'登录信息过期，请先登录');
                return FALSE;
            }

            //token验证
            if (FALSE == $this->isvalidate()) {
                MyApp::setResponse(3002,'登录信息过期，请先登录');
                return FALSE;
            }

            MyApp::$preset->set('uid',$this->uid);
            return TRUE;
        }catch (\Exception $e){
            MyApp::setResponse(5000,$e->getMessage());
            return FALSE;
        }
    }

    private function isverify():bool
    {
        $signer = new Sha256();
        $created = $this->token->getClaim('iat');
        $result = (new User(['uid' => $this->uid]))->getLoginInfobyUid();
        list($randNum,$remoteArr) = array_values($result);
        $sign = JwtMaker::makeSign($this->uid,$created,$randNum);
        return $this->token->verify($signer,$sign);
    }

    private function isvalidate():bool
    {
        $validata = new ValidationData();
        $validata->setIssuer(JwtMaker::$issuer);
        $validata->setAudience(JwtMaker::$audience);
        $validata->setId($this->uid);
        return $this->token->validate($validata);
    }
}