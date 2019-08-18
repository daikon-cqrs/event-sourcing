<?php

/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Stream;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\Interop\ToNativeInterface;
use Ds\Map;

final class StreamMap implements \IteratorAggregate, \Countable, ToNativeInterface
{
    /** @var Map */
    private $compositeMap;

    public static function makeEmpty(): self
    {
        return new self;
    }

    public function __construct(StreamInterface ...$commitStream)
    {
        $this->compositeMap = new Map($commitStream);
    }

    public function register(StreamInterface $commitStream): self
    {
        $copy = clone $this;
        $copy->compositeMap->put((string) $commitStream->getAggregateId(), $commitStream);
        return $copy;
    }

    public function unregister(AggregateIdInterface $aggregateId): self
    {
        $copy = clone $this;
        $copy->compositeMap->remove((string) $aggregateId);
        return $copy;
    }

    public function has(string $key): bool
    {
        return $this->compositeMap->hasKey($key);
    }

    public function get(string $key): StreamInterface
    {
        return $this->compositeMap->get($key);
    }

    public function count(): int
    {
        return count($this->compositeMap);
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

    public function getIterator(): \Traversable
    {
        return $this->compositeMap->getIterator();
    }

    public function __get(string $key): StreamInterface
    {
        return $this->get($key);
    }

    private function __clone()
    {
        $this->compositeMap = new Map($this->compositeMap->toArray());
    }
}
