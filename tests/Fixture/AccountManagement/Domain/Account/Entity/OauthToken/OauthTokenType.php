<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\OauthToken;

use Daikon\Entity\Entity\TypedEntityInterface;
use Daikon\Entity\EntityType\Attribute;
use Daikon\Entity\EntityType\AttributeInterface;
use Daikon\Entity\EntityType\EntityType;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\OauthServiceName;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class OauthTokenType extends EntityType
{
    public function __construct(AttributeInterface $parentAttribute)
    {
        parent::__construct("OauthToken", [
            Attribute::define("id", Uuid::class, $this),
            Attribute::define("service", OauthServiceName::class, $this),
            Attribute::define("token_id", Text::class, $this),
            Attribute::define("token", RandomToken::class, $this),
            Attribute::define("expires_at", Timestamp::class, $this)
        ], $parentAttribute);
    }

    public function makeEntity(array $tokenState = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        $tokenState["@type"] = $this;
        $tokenState["@parent"] = $parent;
        return OauthToken::fromArray($tokenState);
    }
}
