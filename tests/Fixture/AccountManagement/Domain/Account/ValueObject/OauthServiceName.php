<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject;

use Assert\Assertion;
use Accordia\Entity\ValueObject\ValueObjectInterface;

final class OauthServiceName implements ValueObjectInterface
{
    public const FACEBOOK = "facebook";

    public const TWITTER = "twitter";

    public const GITHUB = "github";

    public const ALLOWED_SERVICE_NAMES = [ self::FACEBOOK, self::TWITTER, self::GITHUB ];

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @param string $serviceName
     * @return OauthServiceName
     */
    public static function fromNative($serviceName): ValueObjectInterface
    {
        return new static($serviceName);
    }

    /**
     * @return OauthServiceName
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
        return $this->serviceName;
    }

    /**
     * @param ValueObjectInterface $accessRole
     * @return bool
     */
    public function equals(ValueObjectInterface $accessRole): bool
    {
        Assertion::isInstanceOf($accessRole, static::class);
        return $this->serviceName === $accessRole->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->serviceName);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     */
    private function __construct(string $serviceName)
    {
        if (!empty($serviceName)) {
            Assertion::inArray($serviceName, self::ALLOWED_SERVICE_NAMES);
        }
        $this->serviceName = $serviceName;
    }
}
