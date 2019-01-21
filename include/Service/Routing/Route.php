<?php
namespace Routing;

class Route
{
    public static function get($path,$action)
    {
        return (new RouteCollection())->set('POST',$path,$action)->add();
    }

    public static function post($path,$action)
    {
        return (new RouteCollection())->set('GET',$path,$action)->add();
    }

    public static function middleware(array $middleware)
    {
        return (new RouteCollection($middleware));
    }
}