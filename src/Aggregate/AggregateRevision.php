<?php

namespace Accordia\Cqrs\Aggregate;

use Assert\Assertion;
use Accordia\Entity\ValueObject\ValueObjectInterface;

final class AggregateRevision implements ValueObjectInterface
{
    /**
     * @var int
     */
    private const INITIAL = 1;

    /**
     * @var int
     */
    private const NONE = 0;

    /**
     * @var int
     */
    private $revision;

    /**
     * @param int $revision
     * @return AggregateRevision
     */
    public static function fromNative($revision): ValueObjectInterface
    {
        return new self($revision);
    }

    /**
     * @return AggregateRevision
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(self::NONE);
    }

    /**
     * @return int
     */
    public function toNative(): int
    {
        return $this->revision;
    }

    /**
     * @return AggregateRevision
     */
    public function increment(): AggregateRevision
    {
        $copy = clone $this;
        $copy->revision++;
        return $copy;
    }

    /**
     * @param ValueObjectInterface $otherValue
     * @return bool
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        Assertion::isInstanceOf($otherValue, static::class);
        return $otherValue->toNative() === $this->revision;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->revision === self::NONE;
    }

    /**
     * @return bool
     */
    public function isInitial(): bool
    {
        return $this->revision === self::INITIAL;
    }

    /**
     * @param AggregateRevision $from
     * @param AggregateRevision $to
     * @return bool
     */
    public function isWithinRange(AggregateRevision $from, AggregateRevision $to)
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    /**
     * @param AggregateRevision $revision
     * @return bool
     */
    public function isGreaterThanOrEqual(AggregateRevision $revision)
    {
        return $this->revision >= $revision->toNative();
    }

    /**
     * @param AggregateRevision $revision
     * @return bool
     */
    public function isLessThanOrEqual(AggregateRevision $revision)
    {
        return $this->revision <= $revision->toNative();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->revision;
    }

    /**
     * @param int $revision
     */
    private function __construct(int $revision)
    {
        $this->revision = $revision;
    }
}
