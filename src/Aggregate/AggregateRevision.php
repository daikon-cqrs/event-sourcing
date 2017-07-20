<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

use Assert\Assertion;

final class AggregateRevision
{
    private const INITIAL = 1;

    private const NONE = 0;

    /** @var int */
    private $revision;

    public static function fromNative(int $revision): AggregateRevision
    {
        return new self($revision);
    }

    public static function makeEmpty(): AggregateRevision
    {
        return new static(self::NONE);
    }

    public function toNative(): int
    {
        return $this->revision;
    }

    public function increment(): AggregateRevision
    {
        $copy = clone $this;
        $copy->revision++;
        return $copy;
    }

    public function equals(AggregateRevision $revision): bool
    {
        Assertion::isInstanceOf($revision, static::class);
        return $revision->toNative() === $this->revision;
    }

    public function isInitial(): bool
    {
        return $this->revision === self::INITIAL;
    }

    public function isEmpty(): bool
    {
        return $this->revision === self::NONE;
    }

    public function isWithinRange(AggregateRevision $from, AggregateRevision $to): bool
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    public function isGreaterThanOrEqual(AggregateRevision $revision): bool
    {
        return $this->revision >= $revision->toNative();
    }

    public function isGreaterThan(AggregateRevision $revision): bool
    {
        return $this->revision > $revision->toNative();
    }

    public function isLessThanOrEqual(AggregateRevision $revision): bool
    {
        return $this->revision <= $revision->toNative();
    }

    public function isLessThan(AggregateRevision $revision): bool
    {
        return $this->revision < $revision->toNative();
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
