<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate\Command;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\AnnotatesAggregate;

trait CommandTrait
{
    use AnnotatesAggregate;

    public function getKnownAggregateRevision(): AggregateRevision
    {
        return $this->{static::getAnnotatedRevision()};
    }

    public function hasKnownAggregateRevision(): bool
    {
        return !$this->getKnownAggregateRevision()->isEmpty();
    }
}
