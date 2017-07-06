<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\DomainEvent;
use Daikon\MessageBus\MessageInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;

final class AccountWasRegistered extends DomainEvent
{
    private $username;

    private $role;

    private $locale;

    public static function viaCommand(RegisterAccount $registerAccount): self
    {
        return new static(
            $registerAccount->getAggregateId(),
            $registerAccount->getRole(),
            $registerAccount->getUsername(),
            $registerAccount->getLocale()
        );
    }

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            AggregateId::fromNative($nativeArray["aggregateId"]),
            AccessRole::fromNative($nativeArray["role"]),
            Username::fromNative($nativeArray["username"]),
            Locale::fromNative($nativeArray["locale"])
        );
    }

    public static function getAggregateRootClass(): string
    {
        return Account::class;
    }

    public function getRole(): AccessRole
    {
        return $this->role;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function toArray(): array
    {
        return array_merge([
            "expiresAt" => $this->expiresAt->toNative(),
            "role" => $this->role->toNative(),
            "locale" => $this->locale->toNative(),
            "username" => $this->username->toNative(),
        ], parent::toArray());
    }

    protected function __construct(
        AggregateId $aggregateId,
        AccessRole $role,
        Username $username,
        Locale $locale,
        Revision $aggregateRevision = null
    ) {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->role = $role;
        $this->username = $username;
        $this->locale = $locale;
    }
}
