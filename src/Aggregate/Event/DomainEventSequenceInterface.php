<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate\Event;

use Countable;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\Interop\FromNativeInterface;
use Daikon\Interop\ToNativeInterface;
use IteratorAggregate;

interface DomainEventSequenceInterface extends IteratorAggregate, Countable, FromNativeInterface, ToNativeInterface
{
    public static function makeEmpty(): DomainEventSequenceInterface;

    public function push(DomainEventInterface $event): DomainEventSequenceInterface;

    public function append(DomainEventSequenceInterface $events): DomainEventSequenceInterface;

    public function resequence(AggregateRevision $aggregateRevision): DomainEventSequenceInterface;

    public function getHeadRevision(): AggregateRevision;

    public function getTailRevision(): AggregateRevision;

    public function getTail(): DomainEventInterface;

    public function getHead(): DomainEventInterface;

    public function getLength(): int;

    public function isEmpty(): bool;

    /** @return int|bool */
    public function indexOf(DomainEventInterface $event);
}
