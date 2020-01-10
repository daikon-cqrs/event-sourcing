<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore\Stream;

use Daikon\DataStructure\TypedMapInterface;
use Daikon\DataStructure\TypedMapTrait;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\Interop\ToNativeInterface;

final class StreamMap implements TypedMapInterface, ToNativeInterface
{
    use TypedMapTrait;

    public function __construct(iterable $streams = [])
    {
        $this->init($streams, [StreamInterface::class]);
    }

    public function register(StreamInterface $stream): self
    {
        return $this->with((string)$stream->getAggregateId(), $stream);
    }

    public function unregister(AggregateIdInterface $aggregateId): self
    {
        return $this->without((string)$aggregateId);
    }

    public static function makeEmpty(): self
    {
        return new self;
    }

    public function toNative(): array
    {
        $this->assertInitialized();
        $streams = [];
        foreach ($this as $key => $stream) {
            $streams[$key] = $stream->toNative();
        }
        return $streams;
    }
}
