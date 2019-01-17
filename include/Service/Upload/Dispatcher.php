<?php
namespace Service\Data;

interface Dispatcher
{
    public function insert(): bool;
    public function cook();
}