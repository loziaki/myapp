<?php
namespace Service;

use Exception;
use Framework\Myapp;
use Model\UploadLog;
use Service\ExcelReader\FileReader;
use Service\Logger\LoggerFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileProccesor
{
    private $uploadLog;

    private $filePath;

    private $reader;

    private $upload_storage_path;
    const STORAGE_DIR = '/upload/';

    const STATUS_SUCCESS = 1;
    const STATUS_WARNING = 3;
    const STATUS_NOTICE = 4;
    const STATUS_DONE = 2;

    const USED_NORMAL = 0;
    const USED_DELETED = 1;
    const USED_REPLACED = 2;

    public function __construct(int $src,UploadedFile $file,FileReader $reader)
    {
        $this->setStorageDir();
        $this->upload_storage_path = ROOT_PATH.TEMP_DIR.self::STORAGE_DIR;
        //先移动文件到可以使用的区
        $this->file = $file;
        $this->reader = $reader;
        $this->uploadLog = new UploadLog([
            'src' => $src,
            'name' => $this->file->getClientOriginalName(),
            'uploaded' => date('Y-m-d H:i:s'),
            'uid' => MyApp::$preset->get('uid')
        ]);
    }

    public function start(): array
    {
        $fileStatus = self::STATUS_DONE;

        try {
            list($fid,$path) = $this->saveFile();
            if ($this->reader->handle($path) && count($this->reader->dataList) > 0) {
                //这里$this->reader->dataList 就是多个 briefDispatcher
                foreach ($this->reader->dataList as $data) {
                    $data->fid = $fid;
                    if (!$data->cook()) {
                        $fileStatus = self::STATUS_WARNING;
                    }
                }
            } else {
                $fileStatus = self::STATUS_WARNING;
            }
        } catch (Exception $e) {
            $fileStatus = self::STATUS_WARNING;
            LoggerFactory::get('db')->saveLog([
                'fid' => $fid,
                'code' => $e->getCode(),
                'log' => $e->getMessage()
            ]);
        } 
        $this->uploadLog->setValues(['status' => $fileStatus])->updateStatus();
        return [
            'fid' => $fid,
            'fileStatus' => $fileStatus,
            'data' => $this->reader->dataList
        ];

    }

    private function saveFile()
    {
        $fid = $this->uploadLog->insert();
        $filename = $fid.'.'.$this->file->getClientOriginalExtension();
        $this->file->move($this->upload_storage_path,$filename);

        if ($fid == false) {
            throw new Exception('上传文件存储失败，请重新上传',1005);
        }
        return [
            intval($fid),
            $this->upload_storage_path.$filename
        ];
    }

    private function setStorageDir()
    {
        if (!is_dir(ROOT_PATH.TEMP_DIR)) {
            mkdir(ROOT_PATH.TEMP_DIR);
        }

        if (!is_dir(ROOT_PATH.TEMP_DIR.self::STORAGE_DIR)) {
            mkdir(ROOT_PATH.TEMP_DIR.self::STORAGE_DIR);
        }
    }
}