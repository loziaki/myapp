<?php
namespace Service;

use Service\FileProccesor;
use Model\UploadLog; 
use Service\Logger\LoggerFactory;
use Framework\MyApp;
use Service\ExcelReader\FileReader;

class MyUploadRequest
{
    private $hasError;
    private $file;
    private $src;

    private $beforeSave;
    private $checkFunction;

    public static function init(): bool
    {
        //检查所需要的组件是否存在

        //初始化数据库什么的

        //初始化文件夹什么的

        //检查权限什么的
    }

    public function __construct($extension = null,int $src = 0) 
    {
        $this->src = $src;
        $this->file = MyApp::$request->files->get('file');
        if(empty($this->file)) {
            MyApp::setResponse(1000+FileProccesor::STATUS_WARNING,'请选择要上传的文件');
            return;
        }
        
        if (!$extension && $extension != $this->file->getClientOriginalExtension()) {
            MyApp::setResponse(1000+FileProccesor::STATUS_WARNING,'上传文件格式错误');
            $this->hasError = true;
        } else {
            $this->hasError = false;
        }
    }

    public function ready(callable $callback)
    {
        $this->checkFunction = $callback;
        return $this;
    }

    public function check(callable $callback)
    {
        $this->checkFunction = $callback;
        return $this;
    }

    public function process(FileReader $fileReader)
    {
        if ($this->hasError === true || is_null($this->file)) {
            return false;
        }

        $result = (new FileProccesor($this->src,$this->file,$fileReader))->start();
        if ($result['fileStatus'] == FileProccesor::STATUS_DONE) {
            $flag = $this->save($result);
        } else {
            $flag = false;
        }

        if (false == $flag) {
            $msg = LoggerFactory::get('db')->getLog($result['fid']);
            MyApp::setResponse(1000+FileProccesor::STATUS_WARNING,json_encode($msg));
            return false;
        }

        return $result;
    }

    private function save($result): bool
    {
        $oldFid = MyApp::$request->query->getInt('ofid',0);
        list($fid,$status,$datas) = array_values($result);

        if (isset($this->checkFunction) && !call_user_func($this->checkFunction,$fid,$datas)) {
            return false;
        }
        
        if ($oldFid > 0) {
            (new UploadLog([
                'fid' => $oldFid,
                'used' => FileProccesor::USED_REPLACED,
            ]))->updateUsed();
        }

        if (isset($this->beforeSave)) {
            $hook = $this->beforeSave;
            $hook($datas);
        }

        return $this->dispatch($datas);
    }

    private function dispatch(&$datas): bool
    {
        $flag = true;
        try {
            Myapp::$db->beginTransaction(); 
            foreach ($datas as $data) {
                $flag &= $data->insert();
            }
            Myapp::$db->commit();
        } catch(PDOException $e) {
            MyApp::$db->rollBack();
            MyApp::setResponse(1000+FileProccesor::STATUS_WARNING,'exception happened while saving datas');
            return false;
        }
        return boolval($flag);
    }
}