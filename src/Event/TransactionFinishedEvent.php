<?php

namespace DualMedia\DoctrineRetryBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TransactionFinishedEvent extends Event
{
    public function __construct(
        public readonly int $attempt
    ) {
    }
}
