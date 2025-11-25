<?php

namespace DualMedia\DoctrineRetryBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RetryEvent extends Event
{
    public function __construct(
        public readonly \Exception $exception,
        public readonly int $attempt
    ) {
    }
}