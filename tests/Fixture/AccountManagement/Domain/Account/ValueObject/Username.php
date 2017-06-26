<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class Username implements ValueObjectInterface
{
    public const MIN_LENGTH = 2;

    public const MAX_LENGTH = 50;

    /**
     * @var string
     */
    private $username;

    /**
     * @param string $username
     * @return Username
     */
    public static function fromNative($username): ValueObjectInterface
    {
        return new static($username);
    }

    /**
     * @return Username
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static("");
    }

    /**
     * @return string
     */
    public function toNative()
    {
        return $this->username;
    }

    /**
     * @param ValueObjectInterface $accessRole
     * @return bool
     */
    public function equals(ValueObjectInterface $accessRole): bool
    {
        Assertion::isInstanceOf($accessRole, static::class);
        return $this->username === $accessRole->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->username);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    private function __construct(string $username)
    {
        if (!empty($username)) {
            Assertion::betweenLength($username, self::MIN_LENGTH, self::MAX_LENGTH);
        }
        $this->username = $username;
    }
}
