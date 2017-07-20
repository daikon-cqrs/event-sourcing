<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore\Stream;

interface StreamIdInterface
{
    public static function fromNative(string $id): StreamIdInterface;

    public function toNative(): string;

    public function equals(StreamIdInterface $streamId): bool;

    public function __toString(): string;
}
