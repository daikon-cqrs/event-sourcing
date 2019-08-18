<?php

/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Storage;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\EventStore\Stream\StreamIdInterface;
use Daikon\EventSourcing\EventStore\Stream\StreamInterface;
use Daikon\EventSourcing\EventStore\Stream\Sequence;

interface StreamStorageInterface
{
    public function load(
        StreamIdInterface $streamId,
        AggregateRevision $from = null,
        AggregateRevision $to = null
    ): StreamInterface;

    public function append(StreamInterface $stream, Sequence $knownHead): StorageResultInterface;
}
