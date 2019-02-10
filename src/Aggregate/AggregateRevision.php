<?php
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\Aggregate;

use Assert\Assertion;
use Daikon\Interop\ValueObjectInterface;

final class AggregateRevision implements ValueObjectInterface
{
    private const INITIAL = 1;

    private const NONE = 0;

    /** @var int */
    private $revision;

    /** @param int $revision */
    public static function fromNative($revision): AggregateRevision
    {
        return new self($revision);
    }

    public static function makeEmpty(): AggregateRevision
    {
        return new self(self::NONE);
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

    public function equals(ValueObjectInterface $revision): bool
    {
        Assertion::isInstanceOf($revision, self::class);
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
