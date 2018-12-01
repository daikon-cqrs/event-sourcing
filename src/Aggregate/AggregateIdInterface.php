<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

use Daikon\Interop\FromNativeInterface;
use Daikon\Interop\ToNativeInterface;

interface AggregateIdInterface extends FromNativeInterface, ToNativeInterface
{
    public function equals(AggregateIdInterface $aggregateId): bool;

    public function __toString(): string;
}
