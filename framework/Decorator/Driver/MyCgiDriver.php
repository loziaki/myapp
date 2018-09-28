<?php declare(strict_types=1);
namespace Framework\Decorator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Framework\MyApp;

class MyCgiDriver extends Driver
{
    public function ready()
    {
        MyApp::$request = new Request($_GET,$_POST,array(),$_COOKIE,$_FILES,$_SERVER);
    }

    public function getPath(): string
    {
        return MyApp::$request->query->get('_path');
    }

    public function getMethod(): string
    {
        return MyApp::$request->getMethod();
    }

    public function success()
    {
        $resJson = $this->createResponse();
        $response = new Response($resJson,Response::HTTP_OK,$this->getHeaders());
        $response->send();
    }

    public function exception()
    {
        $response = new Response('',Response::HTTP_NOT_FOUND,$this->getHeaders());
        $response->send(); 
    }

    public function error()
    {
        $response = new Response('',Response::HTTP_INTERNAL_SERVER_ERROR,$this->getHeaders());
        $response->send(); 
    }

    private function createResponse(): string
    {
        list($status,$msg,$res) = array_values(MyApp::getResponse());

        if (is_string($msg) && !empty($msg)) {
            if (in_array($msg[0],['{','['])) {
                $msg = $msg;
            } else {
                $msg = '"'.$msg.'"';
            }
        } else {
            $msg = 'null';
        }
        
        if (is_string($res) && !empty($res)) {
            if (in_array($res[0],['{','['])) {
                $res = $res;
            } else {
                $res = '"'.$res.'"';
            }
        } else {
            $res = '"'.(string)$res.'"';;
        }

        return sprintf('{"status":%d,"msg":%s,"res":%s}', 
            intval($status), $msg, $res
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