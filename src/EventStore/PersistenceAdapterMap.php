<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\DataStructures\TypedMapTrait;

final class PersistenceAdapterMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    public function __construct(array $persistenceAdapters = [])
    {
        $this->init($persistenceAdapters, PersistenceAdapterInterface::class);
    }
}
