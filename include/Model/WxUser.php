<?php
namespace Model;

use Service\Model\DataModel;
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

    public function save()
    {
        $this->uid = $this->insert();
        return $this->uid;
    }
}
