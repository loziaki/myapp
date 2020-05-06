<?php
namespace Service;

class Translation
{
    const MAP = [
        'uid ' => '用户id',
        'uidKey' => '用户key',
        'name' => '名称',
        'phone' => '手机号',
        'gender' => '性别',
        'birth' => '生日',
        'school' => '所在学校',
        'grade' => '所在年级',
        'email' => '邮箱',
        'wxid' => '微信id',
        'ctype' => '证件类型',
        'cnum' => '证件号',
        'description' => '简介',
        'activeName' => '联系人名称',
        'aRel' => '联系人关系',
        'aPhone' => '联系人手机号',
        'teamId' => '队伍id',
        'teamRole' => '队伍角色',
        'create_time' => '创建时间',
    ];

    public static function get($english)
    {
        return (array_key_exists($english, self::MAP))? self::MAP[$english] : $english;
    }
}