<?php declare(strict_types=1);

namespace Service\ExcelReader;

interface FileReader
{
    public function handle(string $path): bool;
}