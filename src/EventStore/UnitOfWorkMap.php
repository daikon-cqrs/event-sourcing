<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore;

use Daikon\DataStructure\TypedMap;

final class UnitOfWorkMap extends TypedMap
{
    public function __construct(iterable $unitsOfWork = [])
    {
        $this->init($unitsOfWork, [UnitOfWorkInterface::class]);
    }
}
