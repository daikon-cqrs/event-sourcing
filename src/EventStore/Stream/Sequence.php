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

final class Sequence implements FromNativeInterface, ToNativeInterface
{
    private const INITIAL = 1;

    private const NONE = 0;

    /** @var int */
    private $seqNumber;

    /** @param int $seqNumber */
    public static function fromNative($seqNumber): Sequence
    {
        return new self($seqNumber);
    }

    public static function makeInitial(): Sequence
    {
        return new self(self::NONE);
    }

    public function toNative(): int
    {
        return $this->seqNumber;
    }

    public function increment(): Sequence
    {
        $copy = clone $this;
        $copy->seqNumber++;
        return $copy;
    }

    public function decrement(): Sequence
    {
        $copy = clone $this;
        $copy->seqNumber--;
        return $copy;
    }

    public function equals(Sequence $seqNumber): bool
    {
        return $seqNumber->toNative() === $this->seqNumber;
    }

    public function isInitial(): bool
    {
        return $this->seqNumber === self::INITIAL;
    }

    public function isWithinRange(Sequence $from, Sequence $to): bool
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    public function isGreaterThanOrEqual(Sequence $seqNumber): bool
    {
        return $this->seqNumber >= $seqNumber->toNative();
    }

    public function isLessThanOrEqual(Sequence $seqNumber): bool
    {
        return $this->seqNumber <= $seqNumber->toNative();
    }

    public function __toString(): string
    {
        return (string) $this->seqNumber;
    }

    private function __construct(int $seqNumber)
    {
        $this->seqNumber = $seqNumber;
    }
}
