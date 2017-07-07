<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\CommandHandler;

use Daikon\EventSourcing\Aggregate\CommandHandler;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;

final class RegisterAccountHandler extends CommandHandler
{
    protected function handleRegisterAccount(RegisterAccount $registerAccount, Metadata $metadata): bool
    {
        return $this->commit(Account::register($registerAccount), $metadata);
    }
}
