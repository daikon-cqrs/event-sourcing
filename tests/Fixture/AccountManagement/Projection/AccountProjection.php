<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Projection;

use Daikon\Cqrs\Projection\ProjectionInterface;
use Daikon\Cqrs\Projection\ProjectionTrait;
use Daikon\Entity\Entity\Entity;
use Daikon\Entity\ValueObject\ValueObjectInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AccountWasRegistered;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AuthenticationTokenWasAdded;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\OauthAccountWasRegistered;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\OauthTokenWasAdded;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\PasswordTokenWasAdded;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\VerificationTokenWasAdded;

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
        /* @var \Daikon\Entity\EntityType\NestedEntityListAttribute $tokenList */
        $tokensAttribute = $this->getEntityType()->getAttribute("tokens");
        $tokenType = $tokensAttribute->getValueType()->get($type);
        /* @var \Daikon\Entity\Entity\NestedEntity $token */
        $token = $tokenType->makeEntity($tokenPayload, $this);
        /* @var AccountEntity $accountState */
        $accountState = $this->withValue("tokens", $this->get("tokens")->push($token));
        return $accountState;
    }
}
