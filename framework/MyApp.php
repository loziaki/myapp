<?php declare(strict_types=1);
namespace Framework;

use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;

class MyApp
{
    const CODE_EXCEPTION = -1;
    const CODE_ERROR           = -2;

    const DEFAULT_HEADERS = [
        'content-type' => 'application/json',
        'charset' => 'utf-8',
        // 'Access-Control-Allow-Origin' => '*'
    ];

    //数据库参数
    public static $db;

    public static function create()
    {
        return new static();
    }

    /**
     * $code int
     * $msg 错误信息
     */
    public static function setResponse()
    {

    }

    /**
     *  $view \Framework\View
     */
    public function handle($view)
    {
        try {
            //给view设置一个全局db
            if (defined('DB_TYPE')) {
                $view->db = \Service\Util::getMySQLInstrance();
            }

            //使用 symfony/http-foundation
            // https://symfony.com/doc/current/components/http_foundation.html#installation
            $req = Request::createFromGlobals();

            //开始处理
            $res = $view->dispatch($req);
        } catch (\Exception $e) {
            $res = $this->exception($e);
        } catch (\Error $err) {
            $res = $this->error($err);
        }
        if ($res instanceof Response) {
            $response->headers->add(self::DEFAULT_HEADERS);
            $response->send();
        } else {
            header("Content-type: text/html; charset=utf-8");
        }
        exit;
    }

    /**
     * $e \Exception
     */
    public function exception($e)
    {
        if (defined('ENV') && ENV === 'dev') {
            $resBody = [
                'code' => CODE_EXCEPTION,
                'msg' => $e->getMessage(),
                'trace' => $e->getTrace()
            ];
            $resJson = json_encode($resBody, JSON_UNESCAPED_UNICODE);
            $response = new Response($resJson, Response::HTTP_OK);
            $response->send();
        } else {
            $response = new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->send();
        }

        return $response;
    }

    /**
     *  $err Error
     */
    public function error($err)
    {
        if (defined('ENV') && ENV === 'dev') {
            $resBody = [
                'code' => CODE_ERROR,
                'msg' => $e->getMessage(),
                'trace' => $e->getTrace()
            ];
            $resJson = json_encode($resBody, JSON_UNESCAPED_UNICODE);
            $response = new Response($resJson, Response::HTTP_OK);
            $response->send();
        } else {
            $response = new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->send();
        }

        return $response;
    }
}
