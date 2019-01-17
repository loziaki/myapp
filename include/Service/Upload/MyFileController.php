<?php
namespace Controller;

use Framework\MyApp;
use Model\UploadLog;
use Model\Project;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;

class MyFileController
{
    const FILE_NORMAL = 0;
    const FILE_DELETED = 1;
    const FILE_REPLACED = 2;

    public function deleteFileAction()
    {
        $fid = MyApp::$request->query->getInt('fid',0);
        return $this->updateFileUsed($fid,self::FILE_DELETED);
    }
    
    public function recoverFileAction()
    {
        $fid = MyApp::$request->query->getInt('fid',0);
        return $this->updateFileUsed($fid,self::FILE_NORMAL);
    }

    public function updateFileUsed(int $fid,int $used = 1)
    {
        if (empty($fid)) {
            MyApp::setResponse(0,'query params have problem');
            return 'fail';
        }

        if (!in_array($used,[self::FILE_DELETED,self::FILE_NORMAL])) {
            MyApp::setResponse(0,'query params have problem');
            return 'fail';
        }

        //先将project至为不可用
        (new Project([
            'fid' => $fid,
            'used' => $used
        ]))->updateUsedByFids();

        //再将文件的状态更新
        (new UploadLog([
            'fid' => $fid,
            'used' => $used
        ]))->updateUsed();

        return 'success';
    }

    public function downloadFileAction()
    {
        $fid = MyApp::$request->query->getInt('fid',0);

        MyApp::hasResponse(true);
        $result = (new Uploadlog(['fid' => $fid]))->getFileInfoByFid();
        if ($result == false) {
            echo 'ヾ(๑╹◡╹)ﾉ"请求的内容不存在';
            return;
        }
        list($filename,$used,$status) = array_values($result);
        if (preg_match('/^(.+)\.([a-zA-Z]+)$/',$filename,$match)) {
            $realFilename = $fid.'.'.$match[2];
            $filePath = ROOT_PATH.TEMP_DIR.$realFilename;
            if (file_exists($filePath)) {
                $stream = new Stream($filePath);
                $response = new BinaryFileResponse($stream);
                $response->prepare(MyApp::$request);
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $filename
                );
                $response->send();
                return;
            }
        }
        
        echo 'ヾ(๑╹◡╹)ﾉ请求的内容不能访问';
        return;
    }
}