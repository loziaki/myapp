<?php
namespace Framework;

interface MiddlewareInterface
{
    public function handle(): bool;
}