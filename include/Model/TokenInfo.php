<?php
namespace Model;

use Model\DataModel;
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
}
