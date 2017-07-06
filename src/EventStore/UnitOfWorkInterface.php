<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\EventStore;

use Daikon\Cqrs\Aggregate\AggregateIdInterface;
use Daikon\Cqrs\Aggregate\AggregateRootInterface;
use Daikon\MessageBus\Metadata\Metadata;

interface UnitOfWorkInterface
{
    public function commit(AggregateRootInterface $aggregateRoot, Metadata $metadata): CommitSequence;

    public function checkout(
        AggregateIdInterface $aggregateId,
        CommitStreamRevision $revision = null
    ): AggregateRootInterface;
}
