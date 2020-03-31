<?php
namespace Framework;

use \Symfony\Component\HttpFoundation\Response;

class BaseView extends View
{
    const ALLOW_METHOD_NAMES = ['get','post'];

    protected $midlewares = [];

    protected $validater;
    /**
     *  $request \Symfony\Component\HttpFoundation\Request
     */
    public function post($request)
    {
        try {
            $appParams = [];
            if ($this->cook($appParams, $request)  === true) {
                $customParams = $this->validateParam($request);

                $res =  $this->handlePost($appParams, $customParams);
            }
        } catch (\Framework\View\ParamException $e) {
            $res =$this->error($e->getMessage(), $e->getCode());
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
            if ($this->cook($appParams, $request)  === true) {
                $customParams = $this->validateParam($request);

                $res =  $this->handleGet($appParams, $customParams);
            }
        } catch (\Framework\View\ParamException$e) {
            $res =  $this->error($e->getMessage(), $e->getCode());
        }
        return $res;
    }

    protected function cook(&$appParams, $request)
    {
        if (empty($this->middlewares)) {
            //不存在middlewares
            return true;
        }

        foreach ($this->middlewares as $m) {
            $m = '\\Middleware\\'.$m;
            if (!class_exists($m)) {
                return false;
            }
            if (!(new $m())->handle($appParams, $request)) {
                return false;
            }
        }
        return true;
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
     * $msg string
     * $code int
     */
    protected function error($msg, $code = MyApp::CODE_EXCEPTION)
    {
        $resBody = [
            'code' => $code,
            'msg' => $msg,
        ];

        $resJson = json_encode($resBody, JSON_UNESCAPED_UNICODE);
        $response = new Response($resJson, Response::HTTP_OK);
        return $response;
    }

    /**
     * $msg string
     * $code int
     */
    protected function success($data)
    {
        $resBody = $data;
        $resBody['code'] =  MyApp::CODE_SUCCESS;
        $resBody['msg'] = '';

        $resJson = json_encode($resBody, JSON_UNESCAPED_UNICODE);
        $response = new Response($resJson, Response::HTTP_OK);
        return $response;
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
        return [];
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
