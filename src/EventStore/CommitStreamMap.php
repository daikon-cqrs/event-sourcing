<?php

namespace Daikon\Cqrs\EventStore;

use Countable;
use Ds\Map;
use Iterator;
use IteratorAggregate;

final class CommitStreamMap implements IteratorAggregate, Countable
{
    private $compositeMap;

    public static function makeEmpty(): self
    {
        return new self;
    }

    public function __construct(CommitStream ...$commitStream)
    {
        $this->compositeMap = new Map($commitStream);
    }

    public function register(CommitStreamInterface $commitStream): self
    {
        $copy = clone $this;
        $copy->compositeMap->put((string)$commitStream->getStreamId(), $commitStream);
        return $copy;
    }

    public function unregister(CommitStreamInterface $commitStream): self
    {
        $copy = clone $this;
        $copy->compositeMap->remove((string)$commitStream->getStreamId());
        return $copy;
    }

    public function has(string $key): bool
    {
        return $this->compositeMap->hasKey($key);
    }

    public function get(string $key): CommitStream
    {
        return $this->compositeMap->get($key);
    }

    public function count(): int
    {
        return count($this->compositeMap);
    }

    public function toArray(): array
    {
        return $this->compositeMap->toArray();
    }

    public function toNative(): array
    {
        $commitStreams = [];
        foreach ($this->compositeMap as $key => $commitStream) {
            $commitStreams[$key] = $commitStream->toNative();
        }
        return $commitStreams;
    }

    public function isEmpty(): bool
    {
        return $this->compositeMap->isEmpty();
    }

    public function getIterator(): Iterator
    {
        return $this->compositeMap->getIterator();
    }

    public function __get(string $key): CommitStream
    {
        return $this->get($key);
    }

    private function __clone()
    {
        $this->compositeMap = new Map($this->compositeMap->toArray());
    }
}
