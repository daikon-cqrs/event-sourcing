<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\CommandHandler;

use Accordia\MessageBus\MessageBusInterface;
use Accordia\MessageBus\Metadata\Metadata;
use Accordia\Cqrs\Aggregate\CommandHandler;
use Accordia\Cqrs\EventStore\UnitOfWorkInterface;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AccountEntityType;

final class RegisterAccountHandler extends CommandHandler
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
     * @param RegisterAccount $registerAccount
     * @param Metadata $metadata
     * @return bool
     */
    protected function handleRegisterAccount(RegisterAccount $registerAccount, Metadata $metadata): bool
    {
        return $this->commit(
            Account::register($registerAccount, $this->accountStateType),
            $metadata
        );
    }
}
