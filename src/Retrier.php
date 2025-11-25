<?php

namespace DualMedia\DoctrineRetryBundle;

use Doctrine\DBAL\Exception\RetryableException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use DualMedia\DoctrineRetryBundle\Event\TransactionFailedEvent;
use DualMedia\DoctrineRetryBundle\Event\TransactionFinalizedEvent;
use DualMedia\DoctrineRetryBundle\Event\TransactionRetryEvent;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * This service allows for easy retryable database transactions.
 *
 * Try not to nest transactions within each other, instead pass {@link EntityManagerInterface} and {@link Storage} to your classes.
 */
class Retrier
{
    private static int $nesting = 0;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly LoggerInterface|null $logger = null,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly bool $trackNesting = false
    ) {
    }

    /**
     * @template T
     *
     * @param callable(EntityManagerInterface, Storage): T $callback
     *
     * @return T
     */
    public function execute(
        callable $callback,
        int $sleepMilliseconds = 100
    ): mixed {
        if ($this->trackNesting && self::$nesting > 1) {
            $this->logger?->error('[Retrier] Transaction nesting attempt detected', [
                'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
            ]);
        }

        self::$nesting++;

        $storage = new Storage();
        $retries = 0;

        do {
            /** @var EntityManagerInterface $em */
            $em = $this->registry->getManager();
            $em->beginTransaction();

            $rollback = true;

            try {
                $ret = $callback($em, $storage);

                $em->flush();
                $em->commit();

                self::$nesting--;

                $rollback = false;

                return $ret;
            } catch (RetryableException $e) {
                $this->eventDispatcher->dispatch(new TransactionRetryEvent($e, $retries, $em));
                $em->rollback();
                $em->close();
                $this->registry->resetManager();

                $retries++;
                $rollback = false;

                usleep($retries * $sleepMilliseconds * 1000);
            } catch (\Exception $e) {
                $this->logger?->error('[Retrier] An exception has occurred', ['exception' => $e]);
                self::$nesting--;

                $this->eventDispatcher->dispatch(new TransactionFailedEvent($e, $retries, $em));

                throw $e;
            } finally {
                $this->eventDispatcher->dispatch(new TransactionFinalizedEvent($rollback, $retries, $em));

                if ($rollback) {
                    $em->close();

                    $connection = $em->getConnection();

                    if ($connection->isTransactionActive()) {
                        $connection->rollback();
                    }
                }
            }
        } while ($retries < 10);

        self::$nesting--;

        throw $e;
    }
}
