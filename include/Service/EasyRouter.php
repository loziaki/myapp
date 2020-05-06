<?php
namespace Service;

class EasyRouter
{
    public static function path($path)
    {
        $pathArr = explode('/', $path);

        if (empty($pathArr[0])) {
            $pathArr[0] = 'View';
        } else {
            array_unshift($pathArr, 'View');
        }

        array_walk($pathArr, function (&$v, $k) {
            $v = \ucfirst($v);
        });

        $viewName = '\\'.implode('\\', $pathArr);

        if (!\class_exists($viewName)) {
            return new \Framework\View();
        }

        return new $viewName();
    }
}
