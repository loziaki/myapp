<?php
namespace View;

class Test extends \Framework\BaseView
{
    public function handleGet($appParams, $customParams)
    {
        echo 1;
        return;
    }
}
