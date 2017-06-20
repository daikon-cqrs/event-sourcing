<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\OauthToken;

use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\OauthServiceName;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Accordia\Entity\Entity\NestedEntity;
use Accordia\Entity\ValueObject\Text;
use Accordia\Entity\ValueObject\Timestamp;
use Accordia\Entity\ValueObject\Uuid;
use Accordia\Entity\ValueObject\ValueObjectInterface;

final class OauthToken extends NestedEntity
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
     * @return Text
     */
    public function getTokenId(): Text
    {
        return $this->get("token_id");
    }

    /**
     * @return OauthServiceName
     */
    public function getService(): OauthServiceName
    {
        return $this->get("service");
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
