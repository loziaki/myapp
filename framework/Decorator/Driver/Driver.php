<?php declare(strict_types=1);
namespace Framework\Decorator;

abstract class Driver extends Decorator
{
    public function handle(): bool
    {
        try {
            $this->ready();
            $method = $this->getMethod();
            $path = $this->getPath();
            (new MyGuide($this->app,$method,$path))->handle();
            if (false === \Framework\MyApp::isThereOwnRes()) {
                $this->success();
            }
        } catch(Exception $e) {
            $this->exception();
        } catch(Error $err) {
            $this->error();
        }
        return true;
    }

    abstract public function getMethod(): string;
    abstract public function getPath(): string;
    abstract public function ready();
    abstract public function success();
    abstract public function exception();
    abstract public function error();
}