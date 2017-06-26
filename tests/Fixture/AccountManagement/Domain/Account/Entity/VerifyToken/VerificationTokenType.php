<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\VerifyToken;

use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Daikon\Entity\EntityType\Attribute;
use Daikon\Entity\EntityType\AttributeInterface;
use Daikon\Entity\EntityType\EntityType;
use Daikon\Entity\Entity\TypedEntityInterface;
use Daikon\Entity\ValueObject\Uuid;

final class VerificationTokenType extends EntityType
{
    /**
     * @param AttributeInterface $parentAttribute
     */
    public function __construct(AttributeInterface $parentAttribute)
    {
        parent::__construct("VerificationToken", [
            Attribute::define("id", Uuid::class, $this),
            Attribute::define("token", RandomToken::class, $this)
        ], $parentAttribute);
    }

    /**
     * @param array $tokenState
     * @param TypedEntityInterface|null $parent
     * @return TypedEntityInterface
     */
    public function makeEntity(array $tokenState = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        $tokenState["@type"] = $this;
        $tokenState["@parent"] = $parent;
        return VerificationToken::fromArray($tokenState);
    }
}
