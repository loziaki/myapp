<?php
namespace Model\Admin;

use Service\Model\DataModel;
use PDO;

class User extends DataModel
{
    const TABLE_NAME = 'admin_user';

    const TOKEN_EXPIRE_IN = 86400;

    const TABLE_FIELDS = [
        'uid',
        'username',
        'password',
        'token',
        'token_expire',
        'last_login_ip',
        'last_login_time',
    ];

    public function save($uk, $ukValue)
    {
        $this->token_expire = time() + self::TOKEN_EXPIRE_IN;
        $this->last_login_ip = \Service\Util::getIp();

        return $this->update($uk, $ukValue);
    }

    public static function getTokenInfo($token)
    {
        $result = self::select()->field(['token','token_expire','username'])->where('token', $token)->find();

        return $result;
    }

    public static function encryptPassword($password)
    {
        return md5($password.self::TABLE_NAME);
    }
}
