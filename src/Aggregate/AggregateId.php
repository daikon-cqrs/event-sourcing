<?php

namespace Accordia\Cqrs\Aggregate;

use Accordia\Entity\ValueObject\ValueObjectInterface;
use Assert\Assertion;

class AggregateId implements AggregateIdInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     * @return AggregateIdInterface
     */
    public static function fromNative($id): ValueObjectInterface
    {
        return new self(trim($id));
    }

    /**
     * @return AggregateIdInterface
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        throw new \Exception("Creating empty aggregate-ids is not supported.");
    }

    /**
     * @return string
     */
    public function toNative()
    {
        return $this->id;
    }

    /**
     * @param ValueObjectInterface $aggregateId
     * @return bool
     */
    public function equals(ValueObjectInterface $aggregateId): bool
    {
        Assertion::isInstanceOf($aggregateId, static::class);
        return $this->id === $aggregateId->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    private function __construct(string $id)
    {
        Assertion::notEmpty($id);
        $this->id = $id;
    }
}
