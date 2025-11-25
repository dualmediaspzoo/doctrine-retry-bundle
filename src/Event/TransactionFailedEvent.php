<?php

namespace DualMedia\DoctrineRetryBundle\Event;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TransactionFailedEvent extends Event
{
    public function __construct(
        public readonly \Throwable $throwable,
        public readonly int $attempt,
        public readonly EntityManagerInterface|null $em = null,
    ) {
    }
}
