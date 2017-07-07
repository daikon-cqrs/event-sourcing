<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\VerifyToken;

use Daikon\Entity\Entity\TypedEntityInterface;
use Daikon\Entity\EntityType\Attribute;
use Daikon\Entity\EntityType\AttributeInterface;
use Daikon\Entity\EntityType\EntityType;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class VerificationTokenType extends EntityType
{
    public function __construct(AttributeInterface $parentAttribute)
    {
        parent::__construct("VerificationToken", [
            Attribute::define("id", Uuid::class, $this),
            Attribute::define("token", RandomToken::class, $this)
        ], $parentAttribute);
    }

    public function makeEntity(array $tokenState = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        $tokenState["@type"] = $this;
        $tokenState["@parent"] = $parent;
        return VerificationToken::fromArray($tokenState);
    }
}
