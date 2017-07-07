<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class AccessRole implements ValueObjectInterface
{
    public const ADMIN = "admin";

    public const USER = "user";

    public const ALLOWED_ROLES = [ self::ADMIN, self::USER ];

    /** @var string */
    private $role;

    public static function fromNative($role): ValueObjectInterface
    {
        return new static($role);
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        return new static("");
    }

    public function toNative()
    {
        return $this->role;
    }

    public function equals(ValueObjectInterface $accessRole): bool
    {
        Assertion::isInstanceOf($accessRole, static::class);
        return $this->role === $accessRole->toNative();
    }

    public function isEmpty(): bool
    {
        return empty($this->role);
    }

    public function __toString(): string
    {
        return $this->role;
    }

    private function __construct(string $role)
    {
        if (!empty($role)) {
            Assertion::inArray($role, self::ALLOWED_ROLES);
        }
        $this->role = $role;
    }
}
