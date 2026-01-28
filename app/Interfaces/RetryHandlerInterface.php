<?php

namespace App\Interfaces;

interface RetryHandlerInterface
{
    public function execute(callable $operation);
}
