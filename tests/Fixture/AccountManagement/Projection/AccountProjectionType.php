<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Projection;

use Accordia\Cqrs\Aggregate\AggregateId;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AuthToken\AuthenticationTokenType;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\OauthToken\OauthTokenType;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\PasswordToken\PasswordTokenType;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\VerifyToken\VerificationTokenType;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\HashedPassword;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;
use Accordia\Entity\EntityType\Attribute;
use Accordia\Entity\EntityType\EntityType;
use Accordia\Entity\EntityType\NestedEntityListAttribute;
use Accordia\Entity\Entity\TypedEntityInterface;
use Accordia\Entity\ValueObject\Email;
use Accordia\Entity\ValueObject\Text;

class AccountProjectionType extends EntityType
{
    public function __construct()
    {
        parent::__construct("AccountProjection", [
            Attribute::define("id", AggregateId::class, $this),
            Attribute::define("username", Username::class, $this),
            Attribute::define("email", Email::class, $this),
            Attribute::define("role", AccessRole::class, $this),
            Attribute::define("firstname", Text::class, $this),
            Attribute::define("lastname", Text::class, $this),
            Attribute::define("locale", Locale::class, $this),
            Attribute::define("password_hash", HashedPassword::class, $this),
            NestedEntityListAttribute::define("tokens", [
                VerificationTokenType::class,
                AuthenticationTokenType::class,
                PasswordTokenType::class,
                OauthTokenType::class
            ], $this)
        ]);
    }

    /**
     * @param array $entityState
     * @param TypedEntityInterface|null $parent
     * @return TypedEntityInterface
     */
    public function makeEntity(array $entityState = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        $entityState["@type"] = $this;
        $entityState["@parent"] = $parent;
        return AccountProjection::fromArray($entityState);
    }
}
