<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Command;

use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\Command;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\MessageBus\MessageInterface;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\Username;

final class RegisterAccount extends Command
{
    /** @var Timestamp */
    private $expiresAt;

    /** @var AccessRole */
    private $role;

    /** @var Locale */
    private $locale;

    /** @var Username */
    private $username;

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            AggregateId::fromNative($nativeArray["aggregateId"]),
            AccessRole::fromNative($nativeArray["role"]),
            Username::fromNative($nativeArray["username"]),
            Locale::fromNative($nativeArray["locale"]),
            Timestamp::createFromString($nativeArray["expiresAt"])
        );
    }

    public static function getAggregateRootClass(): string
    {
        return Account::class;
    }

    public function getExpiresAt(): Timestamp
    {
        return $this->expiresAt;
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
            "username" => $this->username->toNative()
        ], parent::toArray());
    }

    protected function __construct(
        AggregateIdInterface $aggregateId,
        AccessRole $role,
        Username $username,
        Locale $locale,
        Timestamp $expiresAt
    ) {
        parent::__construct($aggregateId);
        $this->username = $username;
        $this->role = $role;
        $this->locale = $locale;
        $this->expiresAt = $expiresAt;
    }
}
