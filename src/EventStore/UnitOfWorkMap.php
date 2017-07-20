<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\DataStructure\TypedMapTrait;
use Daikon\EventSourcing\Aggregate\AggregateAlias;

final class UnitOfWorkMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    public function __construct(array $unitsOfWork = [])
    {
        $this->init($unitsOfWork, UnitOfWorkInterface::class);
    }

    public function getByAggregateAlias(AggregateAlias $aggregateAlias): UnitOfWorkInterface
    {
        return $this->get((string)$aggregateAlias);
    }
}
