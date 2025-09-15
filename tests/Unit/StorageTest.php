<?php

namespace DualMedia\DoctrineRetryBundle\Tests\Unit;

use DualMedia\DoctrineRetryBundle\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

#[CoversClass(Storage::class)]
class StorageTest extends TestCase
{
    public function testGet(): void
    {
        $storage = new Storage();

        $object = new \stdClass();

        $output = $storage->get('test', fn () => $object);
        static::assertEquals($object, $output);
    }

    #[Depends('testGet')]
    public function testSingleInvoke(): void
    {
        $storage = new Storage();

        $invokable = new class {
            private int $invokeCount = 0;

            public function __invoke(): int
            {
                $this->invokeCount++;

                if ($this->invokeCount > 1) {
                    throw new \RuntimeException('Invoked more than once');
                }

                return 42;
            }
        };

        static::assertEquals(42, $storage->get('val', $invokable));
        static::assertEquals(42, $storage->get('val', $invokable));
        static::assertEquals(42, $storage->get('val', $invokable));
    }
}
