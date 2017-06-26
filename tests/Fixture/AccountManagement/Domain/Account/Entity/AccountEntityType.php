<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AuthToken\AuthenticationTokenType;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\OauthToken\OauthTokenType;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\PasswordToken\PasswordTokenType;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\VerifyToken\VerificationTokenType;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\HashedPassword;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;
use Daikon\Entity\EntityType\Attribute;
use Daikon\Entity\EntityType\EntityType;
use Daikon\Entity\EntityType\NestedEntityListAttribute;
use Daikon\Entity\Entity\TypedEntityInterface;
use Daikon\Entity\ValueObject\Email;
use Daikon\Entity\ValueObject\Text;

final class AccountEntityType extends EntityType
{
    public function __construct()
    {
        parent::__construct("AccountEntity", [
            Attribute::define("identity", AggregateId::class, $this),
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
     * @param mixed[] $accountState
     * @param TypedEntityInterface|null $parent
     * @return TypedEntityInterface
     */
    public function makeEntity(array $accountState = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        $accountState["@type"] = $this;
        $accountState["@parent"] = $parent;
        return AccountEntity::fromArray($accountState);
    }
}
