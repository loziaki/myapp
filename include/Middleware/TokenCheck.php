<?php
namespace Middleware;

use \Service\JwtMaker;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Service\Constant;

class TokenCheck extends \Framework\Middleware
{
    private $token;
    private $uid;

    public function handle(&$customParams, $request)
    {
        $tokenStr= $request->cookies->get(Constant::LOGIN_COOKIE_TOKEN, null);
        //是否登录:cookie检查
        if(empty($tokenStr)) {
            $this->error(1500, '用户未登录，请先登录');
            return false;
        }
        $this->token = (new Parser())->parse((string)$tokenStr);
        $this->uid = $this->token->getHeader('jti');

        try{
            //密钥验证
            if (false == $this->isverify()) {
                $this->error(1501, '登录信息过期，请先登录');
                return false;
            }

            //token验证
            if (false == $this->isvalidate()) {
                $this->error(1502, '登录信息过期，请先登录');
                return false;
            }

            $customParams['uid'] = $this->uid;
            return true;
        } catch (\Exception $e) {
            $this->error(1503, $e->getMessage());
            return false;
        }
    }

    private function isverify()
    {
        $signer = new Sha256();
        $created = $this->token->getClaim('iat');
        // $result = (new User(['uid' => $this->uid]))->getLoginInfobyUid();
        list($randNum,$remoteArr) = array_values($result);
        $sign = JwtMaker::makeSign($this->uid, $created, $randNum);
        return $this->token->verify($signer, $sign);
    }

    private function isvalidate()
    {
        $validata = new ValidationData();
        $validata->setIssuer(JwtMaker::$issuer);
        $validata->setAudience(JwtMaker::$audience);
        $validata->setId($this->uid);
        return $this->token->validate($validata);
    }
}