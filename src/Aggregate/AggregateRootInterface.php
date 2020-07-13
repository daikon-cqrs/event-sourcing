<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate;

use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;

interface AggregateRootInterface
{
    public static function reconstituteFromHistory(
        AggregateIdInterface $identifier,
        DomainEventSequenceInterface $history
    ): self;

    public function getIdentifier(): AggregateIdInterface;

    public function getRevision(): AggregateRevision;

    public function getTrackedEvents(): DomainEventSequenceInterface;
}
