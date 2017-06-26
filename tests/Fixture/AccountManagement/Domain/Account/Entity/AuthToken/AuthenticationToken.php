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

    /**
     * @return Timestamp
     */
    public function getExpiresAt(): Timestamp
    {
        return $this->get("timestamp");
    }
}
