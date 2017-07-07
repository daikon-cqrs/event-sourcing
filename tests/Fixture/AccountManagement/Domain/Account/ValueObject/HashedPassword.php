<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class HashedPassword implements ValueObjectInterface
{
    public const MIN_LENGTH = 2;

    public const MAX_LENGTH = 50;

    /** @var string */
    private $passwordHash;

    public static function fromNative($passwordHash): ValueObjectInterface
    {
        return new static($passwordHash);
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        return new static("");
    }

    public function toNative()
    {
        return $this->passwordHash;
    }

    public function equals(ValueObjectInterface $accessRole): bool
    {
        Assertion::isInstanceOf($accessRole, static::class);
        return $this->passwordHash === $accessRole->toNative();
    }

    public function isEmpty(): bool
    {
        return empty($this->passwordHash);
    }

    public function __toString(): string
    {
        return $this->passwordHash;
    }

    public function verify(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    private function __construct(string $passwordHash)
    {
        if (!empty($passwordHash)) {
            Assertion::betweenLength($passwordHash, self::MIN_LENGTH, self::MAX_LENGTH);
        }
        $this->passwordHash = $passwordHash;
    }
}
