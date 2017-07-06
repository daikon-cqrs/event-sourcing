<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\CommandHandler;

use Daikon\Cqrs\Aggregate\CommandHandler;
use Daikon\Cqrs\EventStore\UnitOfWorkInterface;
use Daikon\MessageBus\MessageBusInterface;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AccountEntityType;

final class RegisterOauthAccountHandler extends CommandHandler
{
    private $accountStateType;

    public function __construct(
        AccountEntityType $accountStateType,
        UnitOfWorkInterface $unitOfWork,
        MessageBusInterface $messageBus
    ) {
        parent::__construct($unitOfWork, $messageBus);
        $this->accountStateType = $accountStateType;
    }

    protected function handleRegisterOauthAccount(RegisterOauthAccount $registerOauthAccount, Metadata $metadata): bool
    {
        return $this->commit(
            Account::registerOauthAccount($registerOauthAccount, $this->accountStateType),
            $metadata
        );
    }
}
