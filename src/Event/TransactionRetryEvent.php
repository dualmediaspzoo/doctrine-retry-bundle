<?php

namespace DualMedia\DoctrineRetryBundle\Event;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TransactionRetryEvent extends Event
{
    public function __construct(
        public readonly \Exception $exception,
        public readonly int $attempt,
        public readonly EntityManagerInterface $em
    ) {
    }
}
