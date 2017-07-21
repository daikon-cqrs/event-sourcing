<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate\Event;

use Daikon\EventSourcing\Aggregate\AggregateRevision;

interface DomainEventSequenceInterface extends \IteratorAggregate, \Countable
{
    public static function fromArray(array $eventsArray): DomainEventSequenceInterface;

    public static function makeEmpty(): DomainEventSequenceInterface;

    public function push(DomainEventInterface $event): DomainEventSequenceInterface;

    public function append(DomainEventSequenceInterface $events): DomainEventSequenceInterface;

    public function toNative(): array;

    public function getHeadRevision(): AggregateRevision;

    public function getTailRevision(): AggregateRevision;

    public function getTail(): ?DomainEventInterface;

    public function getHead(): ?DomainEventInterface;

    public function getLength(): int;

    public function isEmpty(): bool;

    public function indexOf(DomainEventInterface $event): int;
}
