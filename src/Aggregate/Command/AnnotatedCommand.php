<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate\Command;

use Daikon\EventSourcing\Aggregate\AggregateAnnotated;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\Interop\FromToNativeTrait;

trait AnnotatedCommand
{
    use AggregateAnnotated;
    use FromToNativeTrait;

    public function getKnownAggregateRevision(): AggregateRevision
    {
        return $this->{static::getAnnotatedRevision()};
    }

    public function hasKnownAggregateRevision(): bool
    {
        return !$this->getKnownAggregateRevision()->isEmpty();
    }
}
