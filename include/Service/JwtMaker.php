<?php
namespace Service;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class JwtMaker
{
    private $builder;
    private $created;

    public static $audience = AUD_CLAIM;
    public static $issuer = JTI_CLAIM;
    public static $key = JWT_KEY;
    
    const RAND_RANGE = [10000,99999];

    public function __construct()
    {
        $this->builder = new Builder();
        $this->builder->setIssuer(self::$issuer)->setAudience(self::$audience);
        $this->created = time();
    }

    public function setCustomFields($str,$Arr)
    {
        $this->builder->set($str,$Arr);
        return $this;
    }

    /*
    * created TIMESTAMP 
    * affect TIMESTAMP
    * peroid INT 
    */
    public function setPeriod(int $affect = 0,int $peroid = 0)
    {
        if ($affect > 0) {
            $this->builder->setNotBefore($affect);
            if ($peroid > 0) {
                $this->builder->setExpiration($affect+$peroid);
            }
        }
        return $this;
    }

    public function createjwt($id,int $randNum)
    {
        $this->builder->setId($id,true);
        $this->builder->setIssuedAt($this->created);
        $this->builder->sign(new Sha256(),self::makeSign($id,$this->created,$randNum));
        return $this->builder->getToken();
    }

    public static function makeSign($uid,$created,$randNum)
    {
        return self::$key.$uid.$created.$randNum;
    }

    public static function getRandNum()
    {
        return mt_rand(SELF::RAND_RANGE[0],SELF::RAND_RANGE[1]);
    }
}