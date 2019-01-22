<?php
namespace MyApp\Routing;

class RouteCompiler
{
    public static function load($classname) {
        $BASE_DIR = dirname(__FILE__).'/';

        Switch($classname) {
            case 'MyApp\Routing\Route':
                require $BASE_DIR.'/Route.php';break;
            case 'MyApp\Routing\RouteCollection':
                require $BASE_DIR.'/RouteCollection.php';break;
        }
    }

    public static function compile($path)
    {
        spl_autoload_register(['MyApp\Routing\RouteCompiler','load'], true, true);

        if (file_exists($path) && preg_match('/\.php$/',$path)) {
            require $path;
        }

        return new self;
    }

    // public function cache($path)
    // {
    //     $routes = \Routing\RouteCollection::getRoutes();

    //     file_put_contents($path,json_encode($routes,JSON_PRETTY_PRINT));

    //     return $this;
    // }

    public function output($path)
    {
        $routes = \MyApp\Routing\RouteCollection::getRoutes();

        $export = var_export($routes,true);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
        $export = join(PHP_EOL, array_filter(["["] + $array));

        file_put_contents($path,'<?php'.PHP_EOL.'return '.$export.';');

        return $this;
    }
}