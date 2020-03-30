<?php
namespace Framework;

trait ErrorModule
{
    private $errModuleMsg = [];

    /**
     * $code  int
     * $msg string
     */
    protected function error($code, $msg)
    {
        $this->errModuleMsg[] = [$code,$msg];
    }

    protected function anyError()
    {
        return !empty($this->errModuleMsg);
    }

    protected function getErrors()
    {
        return $this->errModuleMsg;
    }
}