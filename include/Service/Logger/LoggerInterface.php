<?php
namespace Service\Logger;

interface LoggerInterface
{
    public function saveLog($message): bool;
}
