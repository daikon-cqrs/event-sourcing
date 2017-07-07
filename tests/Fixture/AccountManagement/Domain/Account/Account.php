<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRoot;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\AccountEntity;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Entity\AccountEntityType;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\AccountWasRegistered;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\AuthenticationTokenWasAdded;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\OauthAccountWasRegistered;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\OauthTokenWasAdded;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\PasswordTokenWasAdded;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\VerificationTokenWasAdded;

final class Account extends AggregateRoot
{
    /** @var AccountEntity */
    private $accountState;

    public static function register(RegisterAccount $registerAccount): self
    {
        $account = new self($registerAccount->getAggregateId());
        $account = $account->reflectThat(AccountWasRegistered::viaCommand($registerAccount))
            ->reflectThat(AuthenticationTokenWasAdded::viaCommand($registerAccount))
            ->reflectThat(VerificationTokenWasAdded::viaCommand($registerAccount));
        return $account;
    }

    public static function registerOauth(RegisterOauthAccount $registerOauthAccount): self
    {
        $account = new self($registerOauthAccount->getAggregateId());
        $account = $account->reflectThat(OauthAccountWasRegistered::viaCommand($registerOauthAccount))
            ->reflectThat(AuthenticationTokenWasAdded::viaCommand($registerOauthAccount))
            ->reflectThat(OauthTokenWasAdded::viaCommand($registerOauthAccount));
        return $account;
    }

    protected function whenAccountWasRegistered(AccountWasRegistered $accountWasRegistered)
    {
        $this->accountState = $this->accountState
            ->withIdentity($accountWasRegistered->getAggregateId())
            ->withLocale($accountWasRegistered->getLocale())
            ->withRole($accountWasRegistered->getRole());
    }

    protected function whenOauthAccountWasRegistered(OauthAccountWasRegistered $oauthAccountWasRegistered)
    {
        $this->accountState = $this->accountState
            ->withIdentity($oauthAccountWasRegistered->getAggregateId())
            ->withRole($oauthAccountWasRegistered->getRole());
    }

    protected function whenAuthenticationTokenWasAdded(AuthenticationTokenWasAdded $tokenWasAdded)
    {
        $this->accountState = $this->accountState->withAuthenticationTokenAdded([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ]);
    }

    protected function whenVerificationTokenWasAdded(VerificationTokenWasAdded $tokenWasAdded)
    {
        $this->accountState = $this->accountState->withVerificationTokenAdded([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken()
        ]);
    }

    protected function whenOauthTokenWasAdded(OauthTokenWasAdded $tokenWasAdded)
    {
        $this->accountState = $this->accountState->withOauthTokenAdded([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "token_id" => $tokenWasAdded->getTokenId(),
            "service" => $tokenWasAdded->getService(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ]);
    }

    protected function whenPasswordTokenWasAdded(PasswordTokenWasAdded $tokenWasAdded)
    {
        // @todd implement
    }

    protected function __construct(AggregateIdInterface $aggregateId)
    {
        parent::__construct($aggregateId);
        $this->accountState = (new AccountEntityType)->makeEntity([ "identity" => $aggregateId ]);
    }
}
