<?php

namespace Accordia\Cqrs\EventStore;

use Countable;
use Ds\Map;
use Iterator;
use IteratorAggregate;

final class CommitStreamMap implements IteratorAggregate, Countable
{
    /**
     * @var Map
     */
    private $compositeMap;

    /**
     * @return CommitStreamMap
     */
    public static function makeEmpty(): self
    {
        return new self;
    }

    /**
     * @param CommitStream[] $commitStreams
     */
    public function __construct(CommitStream ...$commitStream)
    {
        $this->compositeMap = new Map($commitStream);
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
    public function unregister(CommitStreamInterface $commitStream): self
    {
        $copy = clone $this;
        $copy->compositeMap->remove((string)$commitStream->getStreamId());
        return $copy;
    }

    /**
     * @param  string
     * @return boolean
     */
    public function has(string $key): bool
    {
        return $this->compositeMap->hasKey($key);
    }

    /**
     * @param string $key
     * @return CommitStream
     */
    public function get(string $key): CommitStream
    {
        return $this->compositeMap->get($key);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->compositeMap);
    }

    /**
     * @return CommitStream[]
     */
    public function toArray(): array
    {
        return $this->compositeMap->toArray();
    }

    /**
     * @return mixed[]
     */
    public function toNative(): array
    {
        $commitStreams = [];
        foreach ($this->compositeMap as $key => $commitStream) {
            $commitStreams[$key] = $commitStream->toNative();
        }
        return $commitStreams;
    }

    /**
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return $this->compositeMap->isEmpty();
    }

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return $this->compositeMap->getIterator();
    }

    /**
     * @param string $key
     * @return CommitStream
     */
    public function __get(string $key): CommitStream
    {
        return $this->get($key);
    }

    private function __clone()
    {
        $this->compositeMap = new Map($this->compositeMap->toArray());
    }
}
