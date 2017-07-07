<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\AggregateRevision;

interface StreamStoreInterface
{
    public function checkout(
        StreamId $streamId,
        AggregateRevision $from = null,
        AggregateRevision $to = null
    ): StreamInterface;

    public function commit(StreamInterface $stream, StreamRevision $knownHead): StoreResultInterface;
}
