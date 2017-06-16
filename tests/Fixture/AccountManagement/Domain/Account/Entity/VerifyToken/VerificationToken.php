<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\VerifyToken;

use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Accordia\Entity\Entity\NestedEntity;
use Accordia\Entity\ValueObject\Uuid;
use Accordia\Entity\ValueObject\ValueObjectInterface;

class VerificationToken extends NestedEntity
{
    /**
     * @return ValueObjectInterface
     */
    public function getIdentity(): ValueObjectInterface
    {
        return $this->getId();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->get("id");
    }

    /**
     * @return RandomToken
     */
    public function getToken(): RandomToken
    {
        return $this->get("token");
    }
}
