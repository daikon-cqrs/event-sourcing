<?php

namespace Accordia\Cqrs\Aggregate;

use Assert\Assertion;
use Accordia\Entity\ValueObject\ValueObjectInterface;

final class Revision implements ValueObjectInterface
{
    /**
     * @var int
     */
    private $revision;

    /**
     * @param int $revision
     * @return Revision
     */
    public static function fromNative($revision): ValueObjectInterface
    {
        return new self($revision);
    }

    /**
     * @return Revision
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(0);
    }

    /**
     * @return int
     */
    public function toNative(): int
    {
        return $this->revision;
    }

    /**
     * @return Revision
     */
    public function increment(): Revision
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
        return $this->revision === 0;
    }

    /**
     * @return bool
     */
    public function isInitial(): bool
    {
        return $this->revision === 1;
    }

    /**
     * @param Revision $from
     * @param Revision $to
     * @return bool
     */
    public function isWithinRange(Revision $from, Revision $to)
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    /**
     * @param Revision $revision
     * @return bool
     */
    public function isGreaterThanOrEqual(Revision $revision)
    {
        return $this->revision >= $revision->toNative();
    }

    /**
     * @param Revision $revision
     * @return bool
     */
    public function isLessThanOrEqual(Revision $revision)
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
