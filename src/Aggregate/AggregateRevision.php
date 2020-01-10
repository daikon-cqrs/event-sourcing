<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate;

use Assert\Assertion;
use Daikon\ValueObject\ValueObjectInterface;

final class AggregateRevision implements ValueObjectInterface
{
    private const INITIAL = 1;

    private const NONE = 0;

    private int $revision;

    /** @param int|string $revision */
    public static function fromNative($revision): self
    {
        Assertion::integerish($revision);
        return new self((int)$revision);
    }

    public static function makeEmpty(): self
    {
        return new self(self::NONE);
    }

    public function toNative(): int
    {
        return $this->revision;
    }

    public function increment(): self
    {
        $copy = clone $this;
        $copy->revision++;
        return $copy;
    }

    /** @param self $comparator */
    public function equals($comparator): bool
    {
        Assertion::isInstanceOf($comparator, self::class);
        return $this->toNative() === $comparator->toNative();
    }

    public function isInitial(): bool
    {
        return $this->revision === self::INITIAL;
    }

    public function isEmpty(): bool
    {
        return $this->revision === self::NONE;
    }

    public function isWithinRange(self $from, self $to): bool
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    public function isGreaterThanOrEqual(self $revision): bool
    {
        return $this->revision >= $revision->toNative();
    }

    public function isGreaterThan(self $revision): bool
    {
        return $this->revision > $revision->toNative();
    }

    public function isLessThanOrEqual(self $revision): bool
    {
        return $this->revision <= $revision->toNative();
    }

    public function isLessThan(self $revision): bool
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
