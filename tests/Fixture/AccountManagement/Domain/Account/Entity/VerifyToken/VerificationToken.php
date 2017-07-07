<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\VerifyToken;

use Daikon\Entity\Entity\NestedEntity;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\Entity\ValueObject\ValueObjectInterface;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class VerificationToken extends NestedEntity
{
    public function getIdentity(): ValueObjectInterface
    {
        return $this->getId();
    }

    public function getId(): Uuid
    {
        return $this->get("id");
    }

    public function getToken(): RandomToken
    {
        return $this->get("token");
    }
}
