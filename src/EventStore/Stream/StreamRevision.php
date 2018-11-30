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

final class StreamRevision implements FromNativeInterface, ToNativeInterface
{
    private const INITIAL = 1;

    private const NONE = 0;

    /** @var int */
    private $revision;

    /** @param int $revision */
    public static function fromNative($revision): StreamRevision
    {
        return new self($revision);
    }

    public static function makeInitial(): StreamRevision
    {
        return new self(self::NONE);
    }

    public function toNative(): int
    {
        return $this->revision;
    }

    public function increment(): StreamRevision
    {
        $copy = clone $this;
        $copy->revision++;
        return $copy;
    }

    public function decrement(): StreamRevision
    {
        $copy = clone $this;
        $copy->revision--;
        return $copy;
    }

    public function equals(StreamRevision $revision): bool
    {
        return $revision->toNative() === $this->revision;
    }

    public function isInitial(): bool
    {
        return $this->revision === self::INITIAL;
    }

    public function isWithinRange(StreamRevision $from, StreamRevision $to): bool
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    public function isGreaterThanOrEqual(StreamRevision $revision): bool
    {
        return $this->revision >= $revision->toNative();
    }

    public function isLessThanOrEqual(StreamRevision $revision): bool
    {
        return $this->revision <= $revision->toNative();
    }

    public function __toString(): string
    {
        return (string)$this->revision;
    }

    private function __construct(int $revision)
    {
        $this->revision = $revision;
    }
}
