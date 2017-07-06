<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class OauthServiceName implements ValueObjectInterface
{
    public const FACEBOOK = "facebook";

    public const TWITTER = "twitter";

    public const GITHUB = "github";

    public const ALLOWED_SERVICE_NAMES = [ self::FACEBOOK, self::TWITTER, self::GITHUB ];

    private $serviceName;

    public static function fromNative($serviceName): ValueObjectInterface
    {
        return new static($serviceName);
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        return new static("");
    }

    public function toNative()
    {
        return $this->serviceName;
    }

    public function equals(ValueObjectInterface $accessRole): bool
    {
        Assertion::isInstanceOf($accessRole, static::class);
        return $this->serviceName === $accessRole->toNative();
    }

    public function isEmpty(): bool
    {
        return empty($this->serviceName);
    }

    public function __toString(): string
    {
        return $this->serviceName;
    }

    private function __construct(string $serviceName)
    {
        if (!empty($serviceName)) {
            Assertion::inArray($serviceName, self::ALLOWED_SERVICE_NAMES);
        }
        $this->serviceName = $serviceName;
    }
}
