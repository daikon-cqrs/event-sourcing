<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account;

use Accordia\Cqrs\Aggregate\AggregateIdInterface;
use Accordia\Cqrs\Aggregate\AggregateRoot;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AccountEntityType;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AccountWasRegistered;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AuthenticationTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\OauthAccountWasRegistered;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\OauthTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\PasswordTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\VerificationTokenWasAdded;

final class Account extends AggregateRoot
{
    /**
     * @var AccountEntity
     */
    private $accountState;

    /**
     * @param RegisterAccount $registerAccount
     * @param AccountEntityType $accountStateType
     * @return Account
     */
    public static function register(RegisterAccount $registerAccount, AccountEntityType $accountStateType): self
    {
        return (new Account($registerAccount->getAggregateId(), $accountStateType))
            ->reflectThat(AccountWasRegistered::viaCommand($registerAccount))
            ->reflectThat(AuthenticationTokenWasAdded::viaCommand($registerAccount))
            ->reflectThat(VerificationTokenWasAdded::viaCommand($registerAccount));
    }

    /**
     * @param RegisterOauthAccount $registerOauthAccount
     * @param AccountEntityType $accountStateType
     * @return Account
     */
    public static function registerOauth(
        RegisterOauthAccount $registerOauthAccount,
        AccountEntityType $accountStateType
    ): self {
        return (new Account($registerOauthAccount->getAggregateId(), $accountStateType))
            ->reflectThat(OauthAccountWasRegistered::viaCommand($registerOauthAccount))
            ->reflectThat(AuthenticationTokenWasAdded::viaCommand($registerOauthAccount))
            ->reflectThat(OauthTokenWasAdded::viaCommand($registerOauthAccount));
    }

    /**
     * @param AccountWasRegistered $accountWasRegistered
     * @return void
     */
    protected function whenAccountWasRegistered(AccountWasRegistered $accountWasRegistered)
    {
        $this->accountState = $this->accountState
            ->withIdentity($accountWasRegistered->getAggregateId())
            ->withLocale($accountWasRegistered->getLocale())
            ->withRole($accountWasRegistered->getRole());
    }

    /**
     * @param OauthAccountWasRegistered $oauthAccountWasRegistered
     * @return void
     */
    protected function whenOauthAccountWasRegistered(OauthAccountWasRegistered $oauthAccountWasRegistered)
    {
        $this->accountState = $this->accountState
            ->withIdentity($oauthAccountWasRegistered->getAggregateId())
            ->withRole($accountWasRegistered->getRole());
    }

    /**
     * @param AuthenticationTokenWasAdded $tokenWasAdded
     * @return void
     */
    protected function whenAuthenticationTokenWasAdded(AuthenticationTokenWasAdded $tokenWasAdded)
    {
        $this->accountState = $this->accountState->addAuthenticationToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ]);
    }

    /**
     * @param VerificationTokenWasAdded $tokenWasAdded
     * @return void
     */
    protected function whenVerificationTokenWasAdded(VerificationTokenWasAdded $tokenWasAdded)
    {
        $this->accountState = $this->accountState->addVerificationToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken()
        ]);
    }

    /**
     * @param OauthTokenWasAdded $tokenWasAdded
     * @return void
     */
    protected function whenOauthTokenWasAdded(OauthTokenWasAdded $tokenWasAdded)
    {
        $this->accountState = $this->accountState->addOauthToken([
            "id" => $tokenWasAdded->getId(),
            "token" => $tokenWasAdded->getToken(),
            "token_id" => $tokenWasAdded->getTokenId(),
            "service" => $tokenWasAdded->getService(),
            "expires_at" => $tokenWasAdded->getExpiresAt()
        ]);
    }

    /**
     * @param PasswordTokenWasAdded $tokenWasAdded
     * @return void
     */
    protected function whenPasswordTokenWasAdded(PasswordTokenWasAdded $tokenWasAdded)
    {
        // @todd implement
    }

    /**
     * @param AggregateIdInterface $aggregateId
     * @param AccountEntityType $accountStateType
     */
    protected function __construct(AggregateIdInterface $aggregateId, AccountEntityType $accountStateType)
    {
        parent::__construct($aggregateId);
        $this->accountState = $accountStateType->makeEntity([ "identity" => $aggregateId ]);
    }
}
