<?php declare(strict_types=1);
namespace Framework\Decorator;

use Framework\Handle;

abstract class Decorator implements Handle
{
    protected $app;

    public function __construct(\Framework\MyApp $app)
    {
        $this->app = $app;
    }

    abstract public function handle(): bool;
}