<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\VerifyToken;

use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Daikon\Entity\Entity\NestedEntity;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class VerificationToken extends NestedEntity
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
