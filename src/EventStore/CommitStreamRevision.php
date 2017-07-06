<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\EventStore;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class CommitStreamRevision implements ValueObjectInterface
{
    private const INITIAL = 1;

    private const NONE = 0;

    /** @var int */
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

    public function increment(): CommitStreamRevision
    {
        $copy = clone $this;
        $copy->revision++;
        return $copy;
    }

    public function decrement(): CommitStreamRevision
    {
        $copy = clone $this;
        $copy->revision--;
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

    public function isWithinRange(CommitStreamRevision $from, CommitStreamRevision $to)
    {
        return $this->isGreaterThanOrEqual($from) && $this->isLessThanOrEqual($to);
    }

    public function isGreaterThanOrEqual(CommitStreamRevision $revision)
    {
        return $this->revision >= $revision->toNative();
    }

    public function isLessThanOrEqual(CommitStreamRevision $revision)
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
