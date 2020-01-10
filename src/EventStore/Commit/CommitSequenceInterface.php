<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore\Commit;

use Countable;
use Daikon\EventSourcing\EventStore\Stream\Sequence;
use Daikon\Interop\FromNativeInterface;
use Daikon\Interop\ToNativeInterface;
use IteratorAggregate;

interface CommitSequenceInterface extends IteratorAggregate, Countable, FromNativeInterface, ToNativeInterface
{
    public static function makeEmpty(): self;

    public function push(CommitInterface $commit): self;

    public function getTail(): CommitInterface;

    public function getHead(): CommitInterface;

    public function get(Sequence $Sequence): CommitInterface;

    public function has(Sequence $Sequence): bool;

    public function getSlice(Sequence $start, Sequence $end): self;

    public function isEmpty(): bool;

    public function revisionOf(CommitInterface $commit): Sequence;
}
