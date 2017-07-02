<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\DataStructures\TypedMapTrait;

final class UnitOfWorkMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    public function __construct(array $unitsOfWork = [])
    {
        $this->init($unitsOfWork, UnitOfWorkInterface::class);
    }
}
