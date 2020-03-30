<?php
namespace View;

class Test extends \Framework\BaseView
{
    public function handleGet($customParams, $request)
    {
        echo 1;
        return;
    }
}
