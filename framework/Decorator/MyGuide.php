<?php declare(strict_types=1);
namespace Framework\Decorator;

class MyGuide extends Decorator
{
    const ROUTES_PATH = ROOT_PATH.'include/routes.php';

    public function __construct(\Framework\MyApp $app,string $method,string $path)
    {
        parent::__construct($app);
        $this->method = $method;
        $this->path = $path;
    }

    public function handle(): bool
    {
        if (!file_exists(self::ROUTES_PATH)) {
            throw new \Exception('there is no routes.php',10101);
        }

        if (empty($this->method) || empty($this->path)) {
            throw new \Exception('bad path parameters',10102);
        }

        $routesMap = require self::ROUTES_PATH;
        if (!isset($routesMap[$this->method][$this->path])) {
            throw new \Exception('no matching router',10105);
        }

        $route = $routesMap[$this->method][$this->path];

        if (is_string($route)) {
            $middlewares = [];
            $action = $route;
        } elseif (is_array($route)) {
            if (isset($route['middleware']) && is_string($route['middleware'])) {
                $middlewares = explode(',',$route['middleware']);
            } else {
                $middlewares = [];
            }
            $action = $route['action'];
        } else {
            throw new \Exception('bad path parameters',10104);
        }

        if (is_string($action) && strpos($action,'@') > 0) {
            $arr = explode('@',$action);
            $cname = 'Controller\\'.$arr[0];
            $aname = $arr[1];
        } else {
            throw new \Exception('bad route config',10103);
        }

        $this->app->setCAM($cname,$aname,$middlewares);
        $this->app->handle();
        return true;
    }
}