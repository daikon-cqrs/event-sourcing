<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Accordia\Entity\ValueObject\ValueObjectInterface;

final class AccessRole implements ValueObjectInterface
{
    public const ADMIN = "admin";

    public const USER = "user";

    public const ALLOWED_ROLES = [ self::ADMIN, self::USER ];

    /**
     * @var string
     */
    private $role;

    /**
     * @param string $role
     * @return AccessRole
     */
    public static function fromNative($role): ValueObjectInterface
    {
        return new static($role);
    }

    /**
     * @return AccessRole
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
        return $this->role;
    }

    /**
     * @param ValueObjectInterface $accessRole
     * @return bool
     */
    public function equals(ValueObjectInterface $accessRole): bool
    {
        Assertion::isInstanceOf($accessRole, static::class);
        return $this->role === $accessRole->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->role);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    private function __construct(string $role)
    {
        if (!empty($role)) {
            Assertion::inArray($role, self::ALLOWED_ROLES);
        }
        $this->role = $role;
    }
}
