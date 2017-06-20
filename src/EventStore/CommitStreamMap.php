<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\DataStructures\TypedMapTrait;

final class CommitStreamMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    /**
     * @param array $commitStream
     */
    public function __construct(array $commitStream = [])
    {
        $this->init($commitStream, CommitStreamInterface::class);
    }

    /**
     * @param CommitStreamInterface $commitStream
     * @return CommitStreamMap
     */
    public function register(CommitStreamInterface $commitStream): self
    {
        $copy = clone $this;
        $copy->compositeMap->put((string)$commitStream->getStreamId(), $commitStream);
        return $copy;
    }

    /**
     * @param CommitStreamInterface $commitStream
     * @return CommitStreamMap
     */
    public function remove(CommitStreamInterface $commitStream): self
    {
        $copy = clone $this;
        $copy->compositeMap->remove((string)$commitStream->getStreamId());
        return $copy;
    }
}
