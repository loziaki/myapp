<?php
namespace Framework;

use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;

class MyApp
{
    const CODE_SUCCESS      = 1;
    const CODE_EXCEPTION = -101;
    const CODE_ERROR           = -102;

    const DEFAULT_HEADERS = [
        'content-type' => 'application/json',
        'charset' => 'utf-8',
    ];

    //是否开启debug
    private static $debug = false;

    public static function create()
    {
        return new static();
    }

    public static function debugMode($bool)
    {
        self::$debug = ($bool === true);
    }

    /**
     *  $view \Framework\View
     */
    public function handle($view)
    {
        try {
            //使用 symfony/http-foundation
            // https://symfony.com/doc/current/components/http_foundation.html#installation
            $req = Request::createFromGlobals();
            $var  = $req;
            //开始处理
            $res = $view->dispatch($req);
        } catch (\Exception $e) {
            $res = self::exception($e);
        } catch (\Error $err) {
            $res = self::error($err);
        }
        if ($res instanceof Response) {
            $res->send();
        } else {
            header("Content-type: text/html; charset=utf-8");
        }
        exit;
    }

    /**
     * $e \Exception
     */
    public static function exception($e)
    {
        $eCode = $e->getCode();
        $eCode = (empty($eCode))? 'undefine' : $eCode;
        $trace  = (self::$debug)? $e->getTrace() : '';

        $resBody = [
            'code' => self::CODE_EXCEPTION,
            'msg' => '['.$eCode.']'.$e->getMessage(),
            'trace' => $trace
        ];
        return self::response($resBody);
    }

    /**
     *  $err Error
     */
    public static function error($e)
    {
        $eCode = $e->getCode();
        $eCode = (empty($eCode))? 'undefine' : $eCode;
        $trace  = (self::$debug)? $e->getTrace() : '';

        $resBody = [
            'code' => self::CODE_ERROR,
            'msg' => '['.$eCode.']'.$e->getMessage(),
            'trace' => $trace
        ];
        return self::response($resBody);
    }

    /**
     *  $data expect array
     */
    public static function response($data)
    {
        $resJson = (is_array($data))? json_encode($data, JSON_UNESCAPED_UNICODE) : '';
        $response = new Response($resJson, Response::HTTP_OK);
        $response ->headers->add(self::DEFAULT_HEADERS);

        return $response;
    }
}
