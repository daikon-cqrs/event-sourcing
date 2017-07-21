<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate\Event;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\MessageBus\MessageInterface;

interface DomainEventInterface extends MessageInterface
{
    public static function getAggregateRootClass(): string;

    public function conflictsWith(DomainEventInterface $otherEvent): bool;

    public function getAggregateId(): AggregateIdInterface;

    public function getAggregateRevision(): AggregateRevision;

    public function withAggregateRevision(AggregateRevision $aggregateRevision): DomainEventInterface;
}
