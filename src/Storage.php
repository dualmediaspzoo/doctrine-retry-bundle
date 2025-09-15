<?php

namespace DualMedia\DoctrineRetryBundle;

class Storage
{
    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * @template T
     *
     * @param callable(): T $fn
     *
     * @return T
     */
    public function get(
        string $key,
        callable $fn
    ): mixed {
        return $this->data[$key] ??= $fn();
    }
}
