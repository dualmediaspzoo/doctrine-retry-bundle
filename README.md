[![Packagist Downloads](https://img.shields.io/packagist/dt/dualmedia/doctrine-retry-bundle)](https://packagist.org/packages/dualmedia/doctrine-retry-bundle)

# Doctrine Retry Bundle

A Symfony Bundle for easy retryable database transactions.

## Install

Simply `composer require dualmedia/doctrine-retry-bundle`

Then add the bundle to your `config/bundles.php` file like so

```php
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    // other bundles ...
    DualMedia\DoctrineRetryBundle\DoctrineRetryBundle::class => ['all' => true],
];
```

## Setup

You're free to leave the configuration as-is, otherwise all you can change is the following:

```yaml
dm_doctrine_retry:
  track_nesting: '%kernel.debug%' # if true, Retrier will warn you if you nest transaction calls
```

## Usage

```php
use DualMedia\DoctrineRetryBundle\Retrier;
use Doctrine\ORM\EntityManagerInterface;

class Foo {
    public function __construct(
        private readonly Retrier $retrier
    ) {}
    
    public function doWork(
        int $orderId
    ): void {
        $this->retrier->execute(function (EntityManagerInterface $em) use ($orderId): void {
            // do some work which may cause deadlocks and such
            $order = $em->getRepository(SomeOrder::class)->find($orderId, \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
        });
    }
}
```
