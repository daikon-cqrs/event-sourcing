<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Commit;

use Daikon\EventSourcing\EventStore\Stream\StreamRevision;
use Daikon\Interop\FromNativeInterface;
use Daikon\Interop\ToNativeInterface;

interface CommitSequenceInterface extends \IteratorAggregate, \Countable, FromNativeInterface, ToNativeInterface
{
    public static function makeEmpty(): CommitSequenceInterface;

    public function push(CommitInterface $commit): CommitSequenceInterface;

    public function getTail(): ?CommitInterface;

    public function getHead(): ?CommitInterface;

    public function get(StreamRevision $streamRevision): ?CommitInterface;

    public function getSlice(StreamRevision $start, StreamRevision $end): CommitSequenceInterface;

    public function isEmpty(): bool;

    public function revisionOf(CommitInterface $commit): StreamRevision;

    public function getLength(): int;
}
