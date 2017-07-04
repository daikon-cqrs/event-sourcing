<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Aggregate;

use Daikon\MessageBus\MessageInterface;

interface DomainEventInterface extends MessageInterface
{
    public function getAggregateId(): AggregateIdInterface;

    public function getAggregateRevision(): AggregateRevision;

    public function withAggregateRevision(AggregateRevision $aggregateRevision): DomainEventInterface;
}
