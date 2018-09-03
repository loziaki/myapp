<?php
namespace Framework;

class MyExchanger
{
    private $arr;

    public function __construct()
    {
        $this->arr = [];
    }

    public function get(string $key)
    {
        if (array_key_exists($key,$this->arr)) {
            return $this->arr[$key];
        }
        return null;
    }

    public function set(string $key,$value,string $prefix = null)
    {
        if (array_key_exists($key,$this->arr)) {
            $this->arr[$prefix.'_'.$key] = $value;
        } else {
            $this->arr[$key] = $value;
        }
    }
}