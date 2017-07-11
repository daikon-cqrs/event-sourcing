<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

interface AggregateRootInterface
{
    public static function getAlias(): AggregateAlias;

    public static function reconstituteFromHistory(
        AggregateIdInterface $aggregateId,
        DomainEventSequenceInterface $history
    ): AggregateRootInterface;

    public function getIdentifier(): AggregateIdInterface;

    public function getRevision(): AggregateRevision;

    public function getTrackedEvents(): DomainEventSequenceInterface;

    public function markClean(): AggregateRootInterface;
}
