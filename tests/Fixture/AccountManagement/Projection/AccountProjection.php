<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Projection;

use Accordia\Cqrs\Projection\ProjectionInterface;
use Accordia\Cqrs\Projection\ProjectionTrait;
use Accordia\Entity\Entity\Entity;
use Accordia\Entity\ValueObject\ValueObjectInterface;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AccountWasRegistered;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AuthenticationTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\OauthAccountWasRegistered;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\OauthTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\PasswordTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\VerificationTokenWasAdded;

final class AccountProjection extends Entity implements ProjectionInterface
{
    use ProjectionTrait;

    public function getIdentity(): ValueObjectInterface
    {
        return $this->get("identity");
    }

    /**
     * @param AccountWasRegistered $accountWasRegistered
     * @return AccountProjection
     */
    protected function whenAccountWasRegistered(AccountWasRegistered $accountWasRegistered): self
    {
        return $this
            ->withValue("id", (string)$accountWasRegistered->getAggregateId())
            ->withValue("locale", $accountWasRegistered->getLocale())
            ->withValue("role", $accountWasRegistered->getRole());
    }

    /**
     * @param OauthAccountWasRegistered $oauthAccountWasRegistered
     * @return AccountProjection
     */
    protected function whenOauthAccountWasRegistered(OauthAccountWasRegistered $oauthAccountWasRegistered): self
    {
        return $this
            ->withValue("id", (string)$oauthAccountWasRegistered->getAggregateId())
            ->withValue("role", $oauthAccountWasRegistered->getRole());
    }

    /**
     * @param AuthenticationTokenWasAdded $tokenWasAdded
     * @return AccountProjection
     */
    protected function whenAuthenticationTokenWasAdded(AuthenticationTokenWasAdded $tokenWasAdded): self
    {
        return $this->addToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ], "authentication_token");
    }

    /**
     * @param VerificationTokenWasAdded $tokenWasAdded
     * @return AccountProjection
     */
    protected function whenVerificationTokenWasAdded(VerificationTokenWasAdded $tokenWasAdded): self
    {
        return $this->addToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken()
        ], "verification_token");
    }

    /**
     * @param OauthTokenWasAdded $tokenWasAdded
     * @return AccountProjection
     */
    protected function whenOauthTokenWasAdded(OauthTokenWasAdded $tokenWasAdded): self
    {
        return $this->addToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "token_id" => $tokenWasAdded->getTokenId(),
            "service" => $tokenWasAdded->getService(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ], "oauth_token");
    }

    /**
     * @param PasswordTokenWasAdded $tokenWasAdded
     * @return AccountProjection
     */
    protected function whenPasswordTokenWasAdded(PasswordTokenWasAdded $tokenWasAdded): self
    {
        return $this->addToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ], "password_token");
    }

    /**
     * @param array $tokenPayload
     * @param string $type
     * @return AccountEntity
     */
    private function addToken(array $tokenPayload, string $type): self
    {
        /* @var \Accordia\Entity\EntityType\NestedEntityListAttribute $tokenList */
        $tokensAttribute = $this->getEntityType()->getAttribute("tokens");
        $tokenType = $tokensAttribute->getValueType()->get($type);
        /* @var \Accordia\Entity\Entity\NestedEntity $token */
        $token = $tokenType->makeEntity($tokenPayload, $this);
        /* @var AccountEntity $accountState */
        $accountState = $this->withValue("tokens", $this->get("tokens")->push($token));
        return $accountState;
    }
}
