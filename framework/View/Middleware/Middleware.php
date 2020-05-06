<?php
namespace Framework;

class Middleware
{
    /**
     *  $request \Symfony\Component\HttpFoundation\Request
     */
    public function handle(&$appParams, $request)
    {
        throw new  \Framework\Middleware\NotFineException('DO NOTHING');
    }
}
