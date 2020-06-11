<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore\Storage;

use Daikon\DataStructure\TypedMap;

final class StreamStorageMap extends TypedMap
{
    public function __construct(iterable $streamStores = [])
    {
        $this->init($streamStores, [StreamStorageInterface::class]);
    }
}
