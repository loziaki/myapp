<?php
namespace Service\Logger;

use Framework\MyApp;
use PDO;
use Exception;

class OprDbLogger implements LoggerInterface
{
    
    public function saveLog($message): bool
    {
        $uid = MyApp::$preset->get('uid');
        if (empty($uid)) {
            throw new Exception('empty uid');
        }

        if (!isset($message['opr']) || !isset($message['effectedId'])) {
            return false;
        }

        $this->save([
            'uid' => $uid,
            'opr' => $message['opr'].'['.$message['effectedId'].']',
            'note' => ($message['note'])?? null,
            'detail_note' => ($message['detail_note'])?? null
        ]);
        return true;
    }

    private function save(array $data)
    {
        $sql = 'INSERT INTO operation_log (uid,opr,note,detailnote,log_time) VALUES (:uid,:opr,:note,:detailnote,NOW())';
        $stat = MyApp::$db->prepare($sql);
        $stat->execute([
            'uid' => $data['uid'],
            'opr' => $data['opr'],
            'note' => $data['note'],
            'detailnote' => $data['detail_note'],
        ]);
    }
}