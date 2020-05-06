<?php
namespace Framework;

use \Symfony\Component\HttpFoundation;

class View
{
    const ALLOW_METHOD_NAMES  = ['get','post','put','patch','delete','purge','options','trace','connect'];

    public function dispatch(HttpFoundation\Request $request)
    {
        $method = strtolower($request->getMethod());

        if (\in_array($method, self::ALLOW_METHOD_NAMES)) {
            if (!method_exists($this, $method)) {
                return $this->httpForbidden();
            } else {
                return $this->$method($request);
            }
        } else {
            return $this->httpMethodNotAllowed();
        }
    }

    public function httpForbidden()
    {
        return  new HttpFoundation\Response('', HttpFoundation\Response::HTTP_FORBIDDEN);
    }

    public function httpMethodNotAllowed()
    {
        return  new HttpFoundation\Response('', HttpFoundation\Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
