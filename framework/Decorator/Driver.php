<?php declare(strict_types=1);
namespace Framework;

abstract class Driver extends Decoractor
{
    public function handle(): bool
    {
        try {
            $this->ready();
            $method = $this->getMethod();
            $path = $this->getPath();
            (new MyGuide($this->app,$method,$path))->handle();
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