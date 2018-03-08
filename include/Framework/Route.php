<?php
namespace framework;

class Route {

    private static $instance = array();

    public static function group(array $config,$function)
    {
        /*
        $config
            group      前缀
            middware    中间件
        */
        
    }

    public static function get($path,$action)
    {
        self::addRoute('GET',$path,$action);
    }

    public static function post($path,$action)
    {
        self::addRoute('POST',$path,$action);
    }

    public static function getRoutes()
    {
        return self::$instance;
    }

    //以下是内部函数
    private $midWare;
    private $prefix;
    private $routeSArr;
    

    private function __construct(array $config)
    {
        $this->midWare = (isset($config['middleWare']))? $config['middleWare']:NULL;
        $this->prefix  = (isset($config['group']))?      $config['group']:NULL;
    }

    private function addRouter($method,$path,$action)
    {
        
    } 
}