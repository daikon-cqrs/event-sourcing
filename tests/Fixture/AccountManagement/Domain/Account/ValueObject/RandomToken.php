<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class RandomToken implements ValueObjectInterface
{
    /** @var null|string */
    private $token;

    public static function generate(): self
    {
        return new static(hash(
            "sha256",
            sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            )
        ));
    }

    public static function fromNative($token): ValueObjectInterface
    {
        return new static($token);
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(null);
    }

    public function toNative()
    {
        return $this->token;
    }

    public function equals(ValueObjectInterface $randomToken): bool
    {
        Assertion::isInstanceOf($randomToken, static::class);
        return $this->token === $randomToken->toNative();
    }

    public function isEmpty(): bool
    {
        return empty($this->token);
    }

    public function __toString(): string
    {
        return $this->token;
    }

    private function __construct(?string $token)
    {
        // @todo assert token pattern
        $this->token = $token;
    }
}
