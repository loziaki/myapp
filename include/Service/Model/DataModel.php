<?php
namespace Service\Model;

use Framework\MyApp;

class DataModel
{
    use QueryBuilderModule;

    const TABLE_NAME = '';

    const TABLE_FIELDS = [];

    protected $db;
    private $kwargs;

    public function __construct(array $param = [])
    {
        $this->db = Util::getMySQLInstrance();

        $this->setValues($param);
    }

    protected function save()
    {
        $param = $this->getParam();

        $fields = array_keys($param);
        $str1 = '(`'.implode('`, ', $fields).'`)';
        $str2 = '(:'.implode(', :', $fields).')';
        $sql ='INSERT '.static::TABLE_NAME.$str1.' VALUES '.$str2;
        $stat = $this->db->prepare($sql);

        $stat->execute($param);
        $pk = $this->db->lastInsertId();
        return $pk;
    }

    protected function update($uk, $ukValue)
    {
        $param = $this->getParam();

        $str1= '';
        foreach ($param as $k => $v) {
            $str1 .= '`'.$k.'` = '.$v;
        }
        $sql ='UPDATE '.static::TABLE_NAME.' SET '.$str1.' WHERE `'.$uk.'` = '.$ukValue;
        $stat = $this->db->prepare($sql);

        $stat->execute($param);
    }

    protected function replace($uk, $ukValue)
    {
        $param = $this->getParam();
        //保证100%有唯一key
        $param[$uk] = $ukValue;

        $fields = array_keys($param);
        $str1 = '(`'.implode('`, ', $fields).'`)';
        $str2 = '(:'.implode(', :', $fields).')';
        $sql ='REPLACE '.static::TABLE_NAME.$str1.' VALUES '.$str2;
        $stat = $this->db->prepare($sql);

        $stat->execute($param);
        $pk = $this->db->lastInsertId();
        return $pk;
    }

    protected function getParam()
    {
        $param =[];
        foreach (static::TABLE_FIELDS as $k) {
            if (array_key_exists($k, $this->kwargs)) {
                $param[$k] = $this->kwargs[$k];
            }
        }

        return $param;
    }

    public function __get($key)
    {
        return $this->kwargs[$key];
    }

    public function __set($key, $value)
    {
        $this->kwargs[$key] = $value;
    }

    public function setValues(array $param)
    {
        $property = $this->fields;

        foreach ($param as $key => $value) {
            if (in_array($key, $property)) {
                $this->kwargs[$key] = $value;
            }
        }

        return $this;
    }
}
