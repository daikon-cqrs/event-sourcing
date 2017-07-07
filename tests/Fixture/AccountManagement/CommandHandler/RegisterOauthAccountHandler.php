<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\CommandHandler;

use Daikon\EventSourcing\Aggregate\CommandHandler;
use Daikon\MessageBus\Metadata\Metadata;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;

final class RegisterOauthAccountHandler extends CommandHandler
{
    protected function handleRegisterOauthAccount(RegisterOauthAccount $registerOauthAccount, Metadata $metadata): bool
    {
        return $this->commit(Account::registerOauth($registerOauthAccount), $metadata);
    }
}
