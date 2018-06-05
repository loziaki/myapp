<?php
namespace Middleware;

use \Framework\MiddlewareInterface;
use \PDO;
use Model\User;
use Framework\MyApp;

class AuthCheck implements MiddlewareInterface
{
    private $uid;
    private $auth;

    public function handle(): bool
    {
        $uid = MyApp::$preset->get('uid');
        //是否登录:cookie检查
        if(empty($uid)){
            MyApp::setResponse(2000,'用户未登录，请先登录');
            return FALSE;
        }

        try{
            //获得用户角色
            $agroupArr = $this->getAGroup($uid);
            //获得用户权限
            $auth = $this->getAuth($agroupArr);
            $path = MyApp::$request->query->get('_path');
            //权限检查
            if(false == in_array($path,$auth)){
                MyApp::setResponse(3005,'权限不足');
                return FALSE;
            }

            return TRUE;
        }catch (\Exception $e){
            MyApp::setResponse(5000,'权限不足');
            return FALSE;
        }
    }
    private function getAuth(array $agArr)
    {
        //权限查询
        //权限列表数据
        $auth = [];
        if (count($agArr) > 0) {
            $agids = implode(',',$agArr);
            $sql = "SELECT p.name FROM permission AS p
                    WHERE p.agid IN ({$agids}) FOR UPDATE";
            $stat = MyApp::$db->prepare($sql);
            $stat->execute();
            $result = $stat->fetchAll(PDO::FETCH_ASSOC);
            if ($result == false) {
                throw new Exception('用户信息错误',2003);
            }
            $auth = array_column($result,'name');
        }
        return $auth;
    }

    private function getAGroup(int $uid)
    {
        //获取权限
        //权限查询
        $agids = (new User(['uid' => $uid]))->getAuthGroupByUid();
        if (count($agids) == 0) {
            throw new Exception('用户信息错误',2002);
        }
        return $agids; 
    }
}