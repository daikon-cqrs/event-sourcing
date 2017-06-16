<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\CommandHandler;

use Accordia\MessageBus\MessageBusInterface;
use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\CommandHandler;
use Accordia\Cqrs\EventStore\UnitOfWorkInterface;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AccountEntityType;

final class RegisterOauthAccountHandler extends CommandHandler
{
    /**
     * @var AccountEntityType
     */
    private $accountStateType;

    /**
     * @param AccountEntityType $accountStateType
     * @param UnitOfWorkInterface $unitOfWork
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        AccountEntityType $accountStateType,
        UnitOfWorkInterface $unitOfWork,
        MessageBusInterface $messageBus
    ) {
        parent::__construct($unitOfWork, $messageBus);
        $this->accountStateType = $accountStateType;
    }

    /**
     * @param RegisterOauthAccount $registerOauthAccount
     * @param Metadata $metadata
     * @return bool
     */
    protected function handleRegisterOauthAccount(RegisterOauthAccount $registerOauthAccount, Metadata $metadata): bool
    {
        return $this->commit(
            Account::registerOauthAccount($registerOauthAccount, $this->accountStateType),
            $metadata
        );
    }
}
