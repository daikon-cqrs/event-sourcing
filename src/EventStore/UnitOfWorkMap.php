<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\AggregatePrefix;
use Daikon\DataStructure\TypedMapTrait;

final class UnitOfWorkMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    public function __construct(array $unitsOfWork = [])
    {
        $this->init($unitsOfWork, UnitOfWorkInterface::class);
    }

    public function getByAggregatePrefix(AggregatePrefix $aggregatePrefix)
    {
        $key = explode('-', $aggregatePrefix->toNative(), 2)[0];
        return $this->get($key);
    }
}
