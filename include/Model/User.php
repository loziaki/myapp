<?php
namespace Model;

use Model\DataModel;
use PDO;

class User extends DataModel
{
    const TABLE_NAME = 'cc_user';

    protected $uid;
    protected $openid;
    protected $nickName;
    protected $avatarUrl;
    protected $gender;
    protected $sessionKey;
    protected $city;
    protected $province;
    protected $country;
    protected $points;
    protected $lastVisitTime;
    protected $createTime;

    public function insert()
    {
        $sql = 'INSERT cc_user(openid,nickname,avatarurl,gender,session_key,city,province,country)
                VALUES (:openid,:nickname,:avatarurl,:gender,:sessionkey,:city,:province,:country)';
        $stat = $this->db->prepare($sql);
        $stat->execute([
            'openid' => $this->openid,
            'nickname' => $this->nickName,
            'avatarurl' => $this->avatarUrl,
            'gender' => intval($this->gender),
            'sessionkey' => $this->sessionKey,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
        ]);
        $this->uid = $this->db->lastInsertId();
        $sql = 'INSERT user_flag(uid) VALUES (?)';
        $stat = $this->db->prepare($sql);
        $stat->execute([$this->uid]);
        return $this->uid;
    }

    public function getUidByOpenid()
    {
        $sql = 'SELECT id FROM cc_user WHERE openid = ?';
        $stat = $this->db->prepare($sql);
        $stat->execute([$this->openid]);
        return $stat->fetchColumn();
    }

    public function updateInfoByUid()
    {
        $sql = 'UPDATE cc_user SET nickname=:nickname,avatarurl=:avatarurl,gender =:gender,
                session_key=:sessionkey,city=:city,province=:province,country=:country,last_visit_time=:tsnow
                WHERE id = :uid';
        $stat = $this->db->prepare($sql);
        $stat->execute([
            'uid' => $this->uid,
            'nickname' => $this->nickName,
            'avatarurl' => $this->avatarUrl,
            'gender' => intval($this->gender),
            'sessionkey' => $this->sessionKey,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'tsnow' => date('Y-m-d H:i:s')
        ]);
    }

    public function getHeadPicUrlByUid()
    {
        $sql = 'SELECT avatarurl FROM cc_user WHERE id = ?';
        $stat = $this->db->prepare($sql);
        $stat->execute([$this->uid]);
        $result = $stat->fetchColumn();
        if ($result == false) {
            return null;
        }
        return $result;
    }

    public function getUid()
    {
        return $this->uid;
    }
}