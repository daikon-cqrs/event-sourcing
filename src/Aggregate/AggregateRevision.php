<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Aggregate;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class AggregateRevision implements ValueObjectInterface
{
    private const INITIAL = 1;

    private const NONE = 0;

    private $revision;

    public static function fromNative($revision): ValueObjectInterface
    {
        return new self($revision);
    }

    public static function makeEmpty(): ValueObjectInterface
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

    public function equals(ValueObjectInterface $otherValue): bool
    {
        Assertion::isInstanceOf($otherValue, static::class);
        return $otherValue->toNative() === $this->revision;
    }

    public function isEmpty(): bool
    {
        return $this->revision === self::NONE;
    }

    public function isInitial(): bool
    {
        return $this->revision === self::INITIAL;
    }

    public function isWithinRange(AggregateRevision $from, AggregateRevision $to)
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    public function isGreaterThanOrEqual(AggregateRevision $revision)
    {
        return $this->revision >= $revision->toNative();
    }

    public function isGreaterThan(AggregateRevision $revision)
    {
        return $this->revision > $revision->toNative();
    }

    public function isLessThanOrEqual(AggregateRevision $revision)
    {
        return $this->revision <= $revision->toNative();
    }

    public function isLessThan(AggregateRevision $revision)
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
