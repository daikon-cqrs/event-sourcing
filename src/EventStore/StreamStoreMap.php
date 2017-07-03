<?php

namespace Daikon\Cqrs\EventStore;

use Daikon\DataStructures\TypedMapTrait;

final class StreamStoreMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    public function __construct(array $streamStores = [])
    {
        $this->init($streamStores, StreamStoreInterface::class);
    }
}
