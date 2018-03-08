<?php
namespace Service;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class JwtMaker
{
    private $builder;

    public static $audience = AUD_CLAIM;
    public static $issuer = JTI_CLAIM;
    public static $key = JWT_KEY;

    public function __construct()
    {
        $this->builder = new Builder();
        $this->builder->setIssuer(self::$issuer)->setAudience(self::$audience);
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
    public function setPeriod(int $created,int $affect = 0,int $peroid = 0)
    {
        $this->builder->setIssuedAt($created);
        if ($affect > 0) {
            $this->builder->setNotBefore($affect);
            if ($peroid > 0) {
                $this->builder->setExpiration($peroid);
            }
        }
        return $this;
    }

    public function createjwt($id)
    {
        $this->builder->setId($id,true);
        $this->builder->setIssuedAt(time());
        $this->builder->sign(new Sha256(),self::$key);
        return $this->builder->getToken();
    }
}