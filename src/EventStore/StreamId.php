<?php

namespace Accordia\Cqrs\EventStore;

use Accordia\Entity\ValueObject\ValueObjectInterface;

class StreamId implements ValueObjectInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @param $identifier
     * @return StreamId
     */
    public static function fromNative($identifier): ValueObjectInterface
    {
        return new self($identifier);
    }

    /**
     * @return StreamId
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(null);
    }

    /**
     * @return string
     */
    public function toNative()
    {
        return $this->identifier;
    }

    /**
     * @param ValueObjectInterface $streamId
     * @return bool
     */
    public function equals(ValueObjectInterface $streamId): bool
    {
        return $this->identifier === $streamId->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->identifier);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    private function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }
}
