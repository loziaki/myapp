<?php declare(strict_types=1);
namespace Framework;

abstract class Decoractor implements Handle
{
    protected $app;

    public function __construct(MyApp $app)
    {
        $this->app = $app;
    }

    abstract public function handle(): bool;
}