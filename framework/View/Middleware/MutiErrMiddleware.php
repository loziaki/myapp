<?php
namespace Framework;

class MutiErrMiddleware extends \Framework\Middleware
{
    use \Framework\ErrorModule;

    public function handle(&$appParams, $request)
    {
        $this->handleCustomLogic($appParams, $request);
        if ($this->anyError()) {
            $err = $this->getErrors();

            $msg = [];
            foreach ($err as $e) {
                $msg[] = $e[1];
            }

            throw new Middleware\NotFineException(implode(';', $msg));
        }
    }

    public function handleCustomLogic($request)
    {
        $this->error('DO NOTHING');
    }
}
