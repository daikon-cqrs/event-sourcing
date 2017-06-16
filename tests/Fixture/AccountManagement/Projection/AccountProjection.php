<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Projection;

use Accordia\Cqrs\Projection\ProjectionInterface;
use Accordia\Cqrs\Projection\ProjectionTrait;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AccountEntity;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AccountWasRegistered;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AuthenticationTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\OauthAccountWasRegistered;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\OauthTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\PasswordTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\VerificationTokenWasAdded;

class AccountProjection extends AccountEntity implements ProjectionInterface
{
    use ProjectionTrait;

    /**
     * @param AccountWasRegistered $accountWasRegistered
     * @return AccountProjection
     */
    protected function whenAccountWasRegistered(AccountWasRegistered $accountWasRegistered): self
    {
        return $this
            ->withId($accountWasRegistered->getAggregateId())
            ->withLocale($accountWasRegistered->getLocale())
            ->withRole($accountWasRegistered->getRole());
    }

    /**
     * @param OauthAccountWasRegistered $oauthAccountWasRegistered
     * @return AccountProjection
     */
    protected function whenOauthAccountWasRegistered(OauthAccountWasRegistered $oauthAccountWasRegistered): self
    {
        return $this
            ->withId($oauthAccountWasRegistered->getAggregateId())
            ->withRole($oauthAccountWasRegistered->getRole());
    }

    /**
     * @param AuthenticationTokenWasAdded $tokenWasAdded
     * @return AccountProjection
     */
    protected function whenAuthenticationTokenWasAdded(AuthenticationTokenWasAdded $tokenWasAdded): self
    {
        return $this->addAuthenticationToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ]);
    }

    /**
     * @param VerificationTokenWasAdded $tokenWasAdded
     * @return AccountProjection
     */
    protected function whenVerificationTokenWasAdded(VerificationTokenWasAdded $tokenWasAdded): self
    {
        return $this->addVerificationToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken()
        ]);
    }

    /**
     * @param OauthTokenWasAdded $tokenWasAdded
     * @return AccountProjection
     */
    protected function whenOauthTokenWasAdded(OauthTokenWasAdded $tokenWasAdded): self
    {
        return $this->addOauthToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "token_id" => $tokenWasAdded->getTokenId(),
            "service" => $tokenWasAdded->getService(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ]);
    }

    /**
     * @param PasswordTokenWasAdded $tokenWasAdded
     * @return AccountProjection
     */
    protected function whenPasswordTokenWasAdded(PasswordTokenWasAdded $tokenWasAdded): self
    {
        return $this->addOauthToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ]);
    }
}
