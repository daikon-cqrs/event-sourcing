<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Storage;

use Daikon\DataStructure\TypedMapTrait;

final class StreamStorageMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    public function __construct(array $streamStores = [])
    {
        $this->init($streamStores, StreamStorageInterface::class);
    }
}
