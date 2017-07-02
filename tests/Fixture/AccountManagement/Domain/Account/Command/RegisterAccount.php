<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\Command;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;

final class RegisterAccount extends Command
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Daikon\Entity\ValueObject\Timestamp
     * @buzz::fromArray->createFromString
     */
    private $expiresAt;

    /**
     * @var \Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole
     * @buzz::fromArray->fromNative
     */
    private $role;

    /**
     * @var \Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale
     * @buzz::fromArray->fromNative
     */
    private $locale;

    /**
     * @var \Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username
     * @buzz::fromArray->fromNative
     */
    private $username;

    public static function getAggregateRootClass(): string
    {
        return Account::class;
    }

    /**
     * @return Timestamp
     */
    public function getExpiresAt(): Timestamp
    {
        return $this->expiresAt;
    }

    /**
     * @return AccessRole
     */
    public function getRole(): AccessRole
    {
        return $this->role;
    }

    /**
     * @return Locale
     */
    public function getLocale(): Locale
    {
        return $this->locale;
    }

    /**
     * @return Username
     */
    public function getUsername(): Username
    {
        return $this->username;
    }

    /**
     * @param AggregateId $aggregateId
     * @param AccessRole $role
     * @param Username $username
     * @param Locale $locale
     * @param Timestamp $expiresAt
     */
    protected function __construct(
        AggregateId $aggregateId,
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
