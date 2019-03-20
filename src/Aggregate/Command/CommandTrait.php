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
use Daikon\EventSourcing\Aggregate\MapsAggregateId;

trait CommandTrait
{
    use MapsAggregateId;

    /** @var AggregateRevision */
    private $knownAggregateRevision;

    public function getKnownAggregateRevision(): AggregateRevision
    {
        return $this->knownAggregateRevision ?? AggregateRevision::makeEmpty();
    }

    public function hasKnownAggregateRevision(): bool
    {
        return !$this->getKnownAggregateRevision()->isEmpty();
    }
}
