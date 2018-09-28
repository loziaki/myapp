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
        $resJson = $this->createMyAppResponse();
        $response = new Response($resJson,Response::HTTP_OK,$this->getHeaders());
        $response->send();
    }

    public function exception(\Exception $e)
    {
        if (defined('ENV') && ENV === 'dev') {
            $resBody = [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            $resJson = $this->createResponse(0,$e->getMessage(),json_encode($resBody));
            $response = new Response($resJson,Response::HTTP_OK,$this->getHeaders());
            $response->send();
        } else {
            $response = new Response('',Response::HTTP_NOT_FOUND,$this->getHeaders());
            $response->send(); 
        }
    }

    public function error(\Error $err)
    {
        if (defined('ENV') && ENV === 'dev') {
            $resBody = [
                'file' => $err->getFile(),
                'line' => $err->getLine()
            ];
            $resJson = $this->createResponse(0,$err->getMessage(),json_encode($resBody));
            $response = new Response($resJson,Response::HTTP_OK,$this->getHeaders());
            $response->send();
        } else {
            $response = new Response('',Response::HTTP_INTERNAL_SERVER_ERROR,$this->getHeaders());
            $response->send(); 
        }
    }

    private function createMyAppResponse(): string
    {
        list($status,$msg,$res) = array_values(MyApp::getResponse());
        return $this->createResponse($status,$msg,$res);
    }

    private function createResponse($status,$msg,$res): string
    {
        if (is_string($msg) && !empty($msg) && in_array($msg[0], ['{','[','"'])) {
            $msg = $msg;
        } else {
            $msg = json_encode($msg);
        }

        if (is_string($res) && !empty($res) && in_array($res[0], ['{','[','"'])) {
            $res = $res;
        } else {
            $res = json_encode($res);
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