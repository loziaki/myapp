<?php
namespace Routing;

class RouteCollection
{
    //group函数中会被设置的变量
    private static $g_middleware = [];
    //传入的group_middleware
    private $groupMiddleware;

    //用于存储这个路由表
    private static $routes;
    //每次添加时的运行时变量
    private $middleware;
    private $method;
    private $path;
    private $action;

    public function __construct($middleware = [])
    {
        $this->groupMiddleware = array_merge(self::$g_middleware,$middleware);
        $this->middleware = [];
    }

    public function group($routes)
    {
        $this->setgroupMiddleware(); 
        $this->loadRoutes($routes);
        $this->resetgroupMiddleware();
    }

    private function loadRoutes($routes)
    {
        if ($routes instanceof \Closure) {
            $routes();
        } else {
            require $routes;
        }
    }

    public function middleware($middleware = [])
    {
        $this->middleware = $middleware;
        $this->add();
    }

    public function set($method,$path,$action)
    {
        $this->method = $method;
        $this->path = $path;
        $this->action = (strpos($action,'@') > 0)? $action : $action.'@index';

        return $this;
    }

    public function add()
    {
        $item = ['action' => $this->action];

        $middlewareArr = array_merge(self::$g_middleware,$this->middleware);
        if (count($middlewareArr) > 0) {
            $item['middleware'] = implode(',',$middlewareArr);
        }
        
        self::$routes[$this->method][$this->path] = $item;

        return $this;
    }

    //去生产一个路由控件
    public static function getRoutes()
    {
        return self::$routes;
        //然后填充到全局变量上
        file_put_contents('test',json_encode(self::$routes,JSON_PRETTY_PRINT));
    }

    public function setgroupMiddleware()
    {
        $temp = self::$g_middleware;
        self::$g_middleware = $this->groupMiddleware;
        $this->groupMiddleware = $temp;
        return $this;
    }

    private function resetgroupMiddleware()
    {
        $temp = self::$g_middleware;
        self::$g_middleware = $this->groupMiddleware;
        $this->groupMiddleware = $temp;
    }
}