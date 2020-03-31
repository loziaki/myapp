<?php
namespace Model;

use Model\DataModel;
use PDO;

class WxUser extends DataModel
{
    const TABLE_NAME = 'wxuser';

    const TABLE_FIELDS = [
        'uid',
        'openId',
        'nickName',
        'avatarUrl',
        'gender',
        'language',
        'province',
        'city',
        'country',
        'create_time',
    ];

    public function insert()
    {
        $this->uid = $this->save();
        return $this->uid;
    }
}
