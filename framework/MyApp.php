<?php declare(strict_types=1);
namespace Framework;

use Service\Util;
use Framework\MyExchanger;
use PDO;

class MyApp implements Handle
{
    const ROUTES_PATH = ROOT_PATH.'include/routes.php';
    const CODE_PROBLEM = 0;
    const CODE_EXCEPTION = -1;
    const CODE_ERROR = -2;
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

    public static function setResponse(int $status = 1000,$msg = null)
    {
        self::$status = $status;
        self::$msg = $msg;
    }

    public static function getResponse()
    {
        return [
            'status' => self::$status,
            'msg' => self::$msg,
            'res' => self::$res,
        ];
    }

    //设置
    public static function hasResponse(bool $flag)
    {
        self::$hasRes = $flag;
    }

    public static function isThereOwnRes()
    {
        return self::$hasRes;
    }

    public static function create()
    {
        //初始化
        self::$hasRes = false;
        self::$status = 1000;
        self::$msg = '';
        self::$res = '';
        
        if (defined('DB_TYPE')) {
            self::$db = Util::getMySQLInstrance();
        }

        if (strtoupper(PHP_SAPI) === 'CLI') {
            return new Decorator\MyCliDriver(new static());
        } else {
            return new Decorator\MyCgiDriver(new static());
        }
    }

    public function __construct($controller = null, $action = 'index',$middlewares = [])
    {
        $this->setCAM($controller,$action,$middlewares);
    }

    public function setCAM($controller,$action,$middlewares)
    {
        $this->middlewares = $middlewares;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function handle(): bool
    {
        if (empty($this->action) || empty($this->controller)) {
            self::setResponse(self::CODE_PROBLEM,'DO NOTHING');
            return false;
        }
        try {
            if ($this->cook()) {
                self::$res = $this->reply();
            }
            return true;
        } catch(Exception $e) {
            self::setResponse(self::CODE_EXCEPTION,'SOME UNCAUGTH EXCEPTION');
            throw $e;
        } catch(Error $err) {
            self::setResponse(self::CODE_ERROR,'SOME UNCAUGTH ERROR');
            throw $err;
        }
    }
    //调用中间件处理整个请求
    private function cook(): bool
    {
        $result = true;
        if (count($this->middlewares) == 0) {
            return true;;
        }

        self::$preset = new MyExchanger();
        foreach ($this->middlewares as $m) {
            $m = '\\Middleware\\'.$m;
            if (!class_exists($m)) {
                return false;
            }
            if (false === (new $m())->handle()) {
                return false;
            }
        }
        return true;
    }

    private function reply()
    {
        $a = $this->action.'Action';
        $c = $this->controller;
        
        if (class_exists($c)) {
            $controller = new $c();
            if (method_exists($controller,$a)) {
                return $controller->$a();
            }
        }
        self::setResponse(self::CODE_PROBLEM,'DO NOTHING');
        return;
    }
}
