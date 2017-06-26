<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class RandomToken implements ValueObjectInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @return RandomToken
     */
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

    /**
     * @param string $token
     * @return RandomToken
     */
    public static function fromNative($token): ValueObjectInterface
    {
        return new static($token);
    }

    /**
     * @return RandomToken
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
        return $this->token;
    }

    /**
     * @param ValueObjectInterface $randomToken
     * @return bool
     */
    public function equals(ValueObjectInterface $randomToken): bool
    {
        Assertion::isInstanceOf($randomToken, static::class);
        return $this->token === $randomToken->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->token);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * @param string $token|null
     */
    private function __construct(?string $token)
    {
        // @todo assert token pattern
        $this->token = $token;
    }
}
