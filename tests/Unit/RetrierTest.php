<?php

declare(strict_types=1);

namespace DualMedia\DoctrineRetryBundle\Tests\Unit;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use DualMedia\DoctrineRetryBundle\Retrier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Pkly\ServiceMockHelperTrait;

#[CoversClass(Retrier::class)]
class RetrierTest extends TestCase
{
    use ServiceMockHelperTrait;

    public function testExecute(): void
    {
        $service = $this->createRealMockedServiceInstance(Retrier::class, [
            'trackNesting' => false,
        ]);

        $em = $this->createMock(EntityManagerInterface::class);

        $this->getMockedService(ManagerRegistry::class)
            ->expects(static::once())
            ->method('getManager')
            ->willReturn($em);

        $service->execute(function () {});
    }
}
