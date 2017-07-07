<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\EventStore;

use Daikon\DataStructure\TypedMapTrait;

final class StreamStoreMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    public function __construct(array $streamStores = [])
    {
        $this->init($streamStores, StreamStoreInterface::class);
    }
}
