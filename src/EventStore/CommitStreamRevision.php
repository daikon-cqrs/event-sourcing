<?php

namespace Accordia\Cqrs\EventStore;

use Assert\Assertion;
use Accordia\Entity\ValueObject\ValueObjectInterface;

final class CommitStreamRevision implements ValueObjectInterface
{
    private const INITIAL = 1;

    private const NONE = 0;

    /**
     * @var int
     */
    private $revision;

    /**
     * @param int $revision
     * @return CommitStreamRevision
     */
    public static function fromNative($revision): ValueObjectInterface
    {
        return new self($revision);
    }

    /**
     * @return CommitStreamRevision
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
     * @return CommitStreamRevision
     */
    public function increment(): CommitStreamRevision
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
     * @param CommitStreamRevision $from
     * @param CommitStreamRevision $to
     * @return bool
     */
    public function isWithinRange(CommitStreamRevision $from, CommitStreamRevision $to)
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    /**
     * @param CommitStreamRevision $revision
     * @return bool
     */
    public function isGreaterThanOrEqual(CommitStreamRevision $revision)
    {
        return $this->revision >= $revision->toNative();
    }

    /**
     * @param CommitStreamRevision $revision
     * @return bool
     */
    public function isLessThanOrEqual(CommitStreamRevision $revision)
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
