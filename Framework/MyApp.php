<?php declare(strict_types=1);
namespace Framework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Service\Logger\LoggerFactory;
use Service\Util;
use Framework\MyExchanger;
use PDO;

class MyApp
{
    const ROUTES_PATH = ROOT_PATH.'/include/routes.php';
    //数据库参数
    public static $db;
    //请求的内容
    public static $request;
    //让controller控制是否需要myapp返回内容
    private static $hasRes;
    //用于给middleware 与 controller通信用
    public static $preset;

    private static $status;
    private static $msg;
    private static $res;
    
    private $middlewares;
    private $controller;
    private $action;
    

    public function __construct()
    {
        $this->middlewares = [];
    }

    public static function create()
    {
        //初始化
        self::$hasRes = false;
        self::$status = 1000;
        self::$msg = '';
        self::$res = '';
        //注册request
        self::$request = new Request(
            $_GET,
            $_POST,
            array(),
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
        self::$db = Util::getMySQLInstrance();
        self::$preset = new MyExchanger();
        return new static();
    }

    public function handle()
    {
        //假如有中间件要去处理
        if (true === $this->prepare()) {
            $response = null;
            //这里要去处理controller里面的action
            //一般中间件会掉用 self::setResponse() 去设置返回内容
            if (true == $this->cook()) {
                //得到返回内容
                $response = $this->reply();
            }
            //如果已经中间件已经有了自己的返回的时候
            if (false === self::$hasRes) {
                //去拼接返回参数
                self::$res = $response;
                $resJson = $this->createResponse();
                $response = new Response($resJson,Response::HTTP_OK,$this->getHeaders());
                $response->send();
            }
        } else {
            $response = new Response('',Response::HTTP_NOT_FOUND,$this->getHeaders());
            $response->send(); 
        }
    }

    public static function setResponse(int $status = 1000,string $msg = '')
    {
        self::$status = $status;
        self::$msg = $msg;
    }
    //设置
    public static function hasResponse(bool $flag)
    {
        self::$hasRes = $flag;
    }

    //在调用接口前的准备
    private function prepare(): bool
    {
        if (!file_exists(self::ROUTES_PATH)) {
            return false;
        }
        $routesMap = require self::ROUTES_PATH;

        $path = self::$request->query->get('_path');
        $method = self::$request->getMethod();

        if (isset($routesMap[$method][$path])) {
            $router = $routesMap[$method][$path];
            if (isset($router['action'])) {
                //如果有中间件就new
                if (isset($router['middleware'])) {
                    $this->prepareMiddleware($router['middleware']);
                }
                //创建action
                return $this->prepareAction($router['action']);
            } elseif (is_string($router)) {
                return $this->prepareAction($router);
            }
        }
        return false;
    }

    private function prepareMiddleware(string $middlewares): bool
    {
        $middlewares = explode(',',$middlewares);
        foreach ($middlewares as $middleware) {
            $mname = 'Middleware\\'.$middleware;
            $middleware = new $mname();
            array_push($this->middlewares,$middleware);
        }
        return true;
    }

    private function prepareAction(string $action): bool
    {
        $arr = explode('@',$action);
        $cname = 'Controller\\'.$arr[0];
        $aname = $arr[1].'Action';
        if (class_exists($cname,TRUE)) {
            $this->controller = new $cname();
            if (method_exists($this->controller,$aname)) {
                $this->action = $aname;
                return true;
            }
        }
        return false;
    }

    //调用中间件处理整个请求
    private function cook(): bool
    {
        $result = true;
        foreach ($this->middlewares as $middleware) {
            $result &= $middleware->handle();
            if (false == $result) {
                break;
            }
        }
        return ($result > 0)? true:false;
    }

    private function reply()
    {
        $action = $this->action;
        $res = $this->controller->$action();
        return $res;
    }

    private function createResponse(): string
    {
        if (is_string(self::$msg)) {
            $msg = '"'.self::$msg.'"';
        } else {
            $msg = 'null';
        }
        
        if (is_string(self::$res)) {
            if (in_array(self::$res[0],['{','['])) {
                $res = self::$res;
            } else {
                $res = '"'.self::$res.'"';
            }
        } else {
            $res = 'null';
        }

        return sprintf('{"status":%d,"msg":%s,"res":%s}', 
            intval(self::$status), $msg, $res
        );
    }

    private function getHeaders(): array
    {
        return [
            'content-type' => 'application/json',
            'charset' => 'utf-8',
            // 'Access-Control-Allow-Origin' => '*'
        ];
    }
}
