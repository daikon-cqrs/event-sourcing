<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AuthToken;

use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Daikon\Entity\Entity\NestedEntity;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\Entity\ValueObject\ValueObjectInterface;

final class AuthenticationToken extends NestedEntity
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

    public function getExpiresAt(): Timestamp
    {
        return $this->get("timestamp");
    }
}
