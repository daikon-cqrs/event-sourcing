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
     * @var \Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AccountEntity
     */
    private $internalState;

    /**
     * @param RegisterAccount $registerAccount
     * @param AccountEntityType $internalStateType
     * @return Account
     */
    public static function register(RegisterAccount $registerAccount, AccountEntityType $internalStateType): self
    {
        return (new Account($internalStateType))
            ->reflectThat(AccountWasRegistered::viaCommand($registerAccount))
            ->reflectThat(AuthenticationTokenWasAdded::viaCommand($registerAccount))
            ->reflectThat(VerificationTokenWasAdded::viaCommand($registerAccount));
    }

    /**
     * @param RegisterOauthAccount $registerOauthAccount
     * @param AccountEntityType $internalStateType
     * @return Account
     */
    public static function registerOauth(
        RegisterOauthAccount $registerOauthAccount,
        AccountEntityType $internalStateType
    ): self {
        return (new Account($internalStateType))
            ->reflectThat(OauthAccountWasRegistered::viaCommand($registerOauthAccount))
            ->reflectThat(AuthenticationTokenWasAdded::viaCommand($registerOauthAccount))
            ->reflectThat(OauthTokenWasAdded::viaCommand($registerOauthAccount));
    }

    /**
     * @return AggregateIdInterface
     */
    public function getIdentifier(): AggregateIdInterface
    {
        return $this->internalState->getIdentity();
    }

    /**
     * @param AccountWasRegistered $accountWasRegistered
     * @return void
     */
    protected function whenAccountWasRegistered(AccountWasRegistered $accountWasRegistered)
    {
        $this->internalState = $this->internalState
            ->withId($accountWasRegistered->getAggregateId())
            ->withLocale($accountWasRegistered->getLocale())
            ->withRole($accountWasRegistered->getRole());
    }

    /**
     * @param OauthAccountWasRegistered $oauthAccountWasRegistered
     * @return void
     */
    protected function whenOauthAccountWasRegistered(OauthAccountWasRegistered $oauthAccountWasRegistered)
    {
        $this->internalState = $this->internalState
            ->withId($oauthAccountWasRegistered->getAggregateId())
            ->withRole($accountWasRegistered->getRole());
    }

    /**
     * @param AuthenticationTokenWasAdded $tokenWasAdded
     * @return void
     */
    protected function whenAuthenticationTokenWasAdded(AuthenticationTokenWasAdded $tokenWasAdded)
    {
        $this->internalState = $this->internalState->addAuthenticationToken([
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
        $this->internalState = $this->internalState->addVerificationToken([
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
        $this->internalState = $this->internalState->addOauthToken([
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
     * @param AccountEntityType $internalStateType
     */
    protected function __construct(AccountEntityType $internalStateType)
    {
        parent::__construct();
        $this->internalState = $internalStateType->makeEntity();
    }
}
