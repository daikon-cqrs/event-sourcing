<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AuthToken;

use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Accordia\Entity\EntityType\Attribute;
use Accordia\Entity\EntityType\AttributeInterface;
use Accordia\Entity\EntityType\EntityType;
use Accordia\Entity\Entity\TypedEntityInterface;
use Accordia\Entity\ValueObject\Timestamp;
use Accordia\Entity\ValueObject\Uuid;

class AuthenticationTokenType extends EntityType
{
    /**
     * @param AttributeInterface $parentAttribute
     */
    public function __construct(AttributeInterface $parentAttribute)
    {
        parent::__construct("AuthenticationToken", [
            Attribute::define("id", Uuid::class, $this),
            Attribute::define("token", RandomToken::class, $this),
            Attribute::define("expires_at", Timestamp::class, $this)
        ], $parentAttribute);
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
        return AuthenticationToken::fromArray($entityState);
    }
}
