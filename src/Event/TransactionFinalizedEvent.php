<?php

namespace DualMedia\DoctrineRetryBundle\Event;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TransactionFinalizedEvent extends Event
{
    public function __construct(
        public readonly bool $success,
        public readonly bool $rollback,
        public readonly int $attempt,
        public readonly EntityManagerInterface $em,
    ) {
    }
}
