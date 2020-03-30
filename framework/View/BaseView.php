<?php
namespace Framework;

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
        $customParams = $this->validateParam($request);
        if ($this->cook($customParams, $request)) {
            return $this->handlePost($customParams, $request);
        }
        return;
    }

    /**
     *  $request \Symfony\Component\HttpFoundation\Request
     */
    public function get($request)
    {
        $customParams = $this->validateParam($request);
        if ($this->cook($customParams, $request)) {
            return $this->handleGet($customParams, $request);
        }
        return;
    }

    protected function cook(&$customParams, $request)
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
            if (!(new $m())->handle($customParams, $request)) {
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
        $customParams = [];
        if (!is_null($this->validater)) {
            //过滤多余的参数，获取正确的类型、格式
            $customParams = $this->validater->program($request);
        }

        //用于判读一些参数与参数之间的逻辑关系
        $customParams = $this->validateCustomLogic($customParams, $request);

        return $customParams;
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
     *  $customParams array
     *  $request                \Symfony\Component\HttpFoundation\Request
     */
    public function handlePost($customParams, $request)
    {
        return;
    }

    /**
     *  $customParams array
     * $request                 \Symfony\Component\HttpFoundation\Request
     */
    public function handleGet($customParams, $request)
    {
        return;
    }
}
