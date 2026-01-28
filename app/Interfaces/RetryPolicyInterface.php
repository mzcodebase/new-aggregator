<?php

namespace App\Interfaces;

interface RetryPolicyInterface
{
    public function execute(callable $operation);
}
