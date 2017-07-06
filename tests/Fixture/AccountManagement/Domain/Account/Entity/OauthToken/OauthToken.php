<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\OauthToken;

use Daikon\Entity\Entity\NestedEntity;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\Entity\ValueObject\ValueObjectInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\OauthServiceName;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class OauthToken extends NestedEntity
{
    public function getIdentity(): ValueObjectInterface
    {
        return $this->getId();
    }

    public function getId(): Uuid
    {
        return $this->get("id");
    }

    public function getTokenId(): Text
    {
        return $this->get("token_id");
    }

    public function getService(): OauthServiceName
    {
        return $this->get("service");
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
