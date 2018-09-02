<?php declare(strict_types=1);
namespace Framework;

class MyGuider extends Decoractor
{
    const ROUTES_PATH = ROOT_PATH.'include/routes.php';

    public function __construct(MyApp $app,string $method,string $path)
    {
        parent::__construct($app);
        $this->method = $method;
        $this->path = $path;
    }

    public function handle(): bool
    {
        if (!file_exists(self::ROUTES_PATH)) {
            throw new \Framework\MyException('there is no routes.php',10101);
        }

        if (empty($this->method) || empty($this->path)) {
            throw new \Framework\MyException('bad path parameters',10102);
        }

        $routesMap = require self::ROUTES_PATH;
        if (!isset($routesMap[$this->method][$this->path])) {
            throw new \Framework\MyException('no matching router',10105);
        }

        $route = $routesMap[$this->method][$this->path];

        if (is_stinrg($route) && strpos($route,'@') > 0) {
            $middlewares = [];

        } elseif (is_array($route)) {
            if (isset($route['middleware']) && is_string($route['middleware'])) {
                $middlewares = explode(',',$route['middleware']);
            } else {
                $middlewares = [];
            }

            if (isset($route['action']) && is_string($route['action']) && strpos($route['action'],'@')) {
                $arr = explode('@',$action);
                $cname = 'Controller\\'.$arr[0];
                $aname = $arr[1];
            } else {
                throw new \Framework\MyException('bad route config',10103);
            }
        } else {
            throw new \Framework\MyException('bad path parameters',10104);
        }

        $this->app->setCAM($cname,$aname,$middlewares);
        return true;
    }
}