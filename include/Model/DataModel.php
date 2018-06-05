<?php
namespace Model;

use Framework\MyApp;

class DataModel
{
    protected $db;

    public function __construct(array $param = [])
    {
        $this->db = MyApp::$db;

        $this->setValues($param);
    }

    public function setValues(array $param) {
        $property = $arr = array_keys(get_class_vars(get_class($this)));

        foreach ($param as $key => $value) {
            if (in_array($key,$property)) {
                $this->$key = $value;
            }
        }

        return $this;
    }
}