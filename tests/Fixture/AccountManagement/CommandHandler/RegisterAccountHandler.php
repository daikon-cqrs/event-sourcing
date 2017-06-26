<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\CommandHandler;

use Daikon\MessageBus\MessageBusInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Cqrs\Aggregate\CommandHandler;
use Daikon\Cqrs\EventStore\UnitOfWorkInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AccountEntityType;

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
