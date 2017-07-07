<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity;

use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\Entity\Entity\TypedEntityInterface;
use Daikon\Entity\EntityType\Attribute;
use Daikon\Entity\EntityType\EntityType;
use Daikon\Entity\EntityType\NestedEntityListAttribute;
use Daikon\Entity\ValueObject\Email;
use Daikon\Entity\ValueObject\Text;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\AuthToken\AuthenticationTokenType;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\OauthToken\OauthTokenType;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\PasswordToken\PasswordTokenType;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\VerifyToken\VerificationTokenType;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\HashedPassword;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\Username;

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

    public function makeEntity(array $accountState = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        $accountState["@type"] = $this;
        $accountState["@parent"] = $parent;
        return AccountEntity::fromArray($accountState);
    }
}
