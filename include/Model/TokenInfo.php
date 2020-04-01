<?php
namespace Model;

use Service\Model\DataModel;
use PDO;

class TokenInfo extends DataModel
{
    const TABLE_NAME = 'token';

    const TOKEN_EXPIRE_IN = 86400;

    const TABLE_FIELDS = [
        'uid',
        'openId',
        'session_key',
        'token',
        'token_expire',
        'last_login_ip',
        'last_login_time',
    ];

    public function save($uk, $ukValue)
    {
        $this->token_expire = time() + self::TOKEN_EXPIRE_IN;
        $this->last_login_ip = \Service\Util::getIp();

        $this->replace($uk, $ukValue);
    }
}