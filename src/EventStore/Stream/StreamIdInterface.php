<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Stream;

use Daikon\Interop\FromNativeInterface;
use Daikon\Interop\ToNativeInterface;

interface StreamIdInterface extends FromNativeInterface, ToNativeInterface
{
    public function equals(StreamIdInterface $streamId): bool;

    public function __toString(): string;
}
