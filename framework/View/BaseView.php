<?php
namespace Framework;

use \Symfony\Component\HttpFoundation\Response;

class BaseView extends View
{
    const ALLOW_ORIGIN_NAME = '';

    const ALLOW_HEADERS_NAMES = [];

    protected $midlewares = [];
    /**
     *  $request \Symfony\Component\HttpFoundation\Request
     */
    public function post($request)
    {
        try {
            $appParams = [];
            $this->cook($appParams, $request);
            $customParams = $this->validateParam($request);

            $res =  $this->handlePost($appParams, $customParams);
        } catch (\Framework\View\ParamException $e) {
            $res =  $this->handleException($e);
        } catch (\Framework\Middleware\NotFineException $e) {
            $res =  $this->handleException($e);
        }
        return $res;
    }

    /**
     *  $request \Symfony\Component\HttpFoundation\Request
     */
    public function get($request)
    {
        try {
            $appParams = [];
            $this->cook($appParams, $request);
            $customParams = $this->validateParam($request);

            $res =  $this->handleGet($appParams, $customParams);
        } catch (\Framework\View\ParamException $e) {
            $res =  $this->handleException($e);
        } catch (\Framework\Middleware\NotFineException $e) {
            $res =  $this->handleException($e);
        }
        return $res;
    }

    public function options($request)
    {
        $res = $this->reply();
        $res->headers->set('Access-Control-Allow-Methods', implode(',', self::ALLOW_METHOD_NAMES));
        $res->headers->set('Content-Length', 0);
        $res->headers->set('Access-Control-Max-Age', 1800);

        return  $res;
    }

    protected function cook(&$appParams, $request)
    {
        if (empty($this->middlewares)) {
            //不存在middlewares
            return;
        }

        foreach ($this->middlewares as $m) {
            if ($m instanceof \Framework\Middleware) {
                $m->handle($appParams, $request);
            }
        }
        return ;
    }


    /**
     *  $request \Symfony\Component\HttpFoundation\Request
     */
    protected function validateParam($request)
    {
        //过滤多余的参数，获取正确的类型、格式
        $customParams = $this->validate($request);

        //用于判读一些参数与参数之间的逻辑关系，还有一些什么字段是否存在
        $customParams = $this->validateCustomLogic($customParams, $request);


        return $customParams;
    }

    /**
     * $e \Exception
     */
    protected function handleException($e)
    {
        $eCode = $e->getCode();
        $eCode = (empty($eCode))? \Framework\MyApp::CODE_EXCEPTION : $eCode;
        $res =  $this->error($e->getMessage(), $eCode);

        return $res;
    }

    /**
     * $msg string
     * $code int
     */
    protected function error($msg, $code = MyApp::CODE_EXCEPTION)
    {
        $resBody = [
            'code' => $code,
            'msg' => $msg,
        ];

        $response = $this->reply($resBody);
        return $response;
    }

    /**
     * $msg string
     * $code int
     */
    protected function success($data, $code = MyApp::CODE_SUCCESS)
    {
        $resBody = $data;
        $resBody['code'] =  $code;
        $resBody['msg'] = '';

        $response = $this->reply($resBody);
        return $response;
    }

    protected function reply($data = '')
    {
        $res = MyApp::response($data);
        if (!empty(static::ALLOW_ORIGIN_NAME)) {
            $res->headers->set('Access-Control-Allow-Origin', static::ALLOW_ORIGIN_NAME);
        }
        if (!empty(static::ALLOW_HEADERS_NAMES)) {
            $res->headers->set('Access-Control-Allow-Headers', implode(',', static::ALLOW_HEADERS_NAMES));
        }

        return $res;
    }

    /**
     *  $request \Symfony\Component\HttpFoundation\Request
     */
    public function validate($request)
    {
        return [];
    }

    /**
     *  $customParams array
     *  $request                \Symfony\Component\HttpFoundation\Request
     */
    protected function validateCustomLogic($customParams, $request)
    {
        return $customParams;
    }

    /**
     * $appParams array
     *  $customParams array
     */
    public function handlePost($appParams, $customParams)
    {
        return;
    }

    /**
     * $appParams array
     *  $customParams array
     */
    public function handleGet($appParams, $customParams)
    {
        return;
    }
}
