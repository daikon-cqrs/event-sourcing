<?php

namespace Daikon\Cqrs\Aggregate;

use Daikon\Entity\ValueObject\ValueObjectInterface;
use Assert\Assertion;

class AggregateId implements AggregateIdInterface
{
    private $id;

    public static function fromNative($id): ValueObjectInterface
    {
        return new self(trim($id));
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        throw new \Exception("Creating empty aggregate-ids is not supported.");
    }

    public function toNative()
    {
        return $this->id;
    }

    public function equals(ValueObjectInterface $aggregateId): bool
    {
        Assertion::isInstanceOf($aggregateId, static::class);
        return $this->id === $aggregateId->toNative();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    private function __construct(string $id)
    {
        Assertion::notEmpty($id);
        $this->id = $id;
    }
}
