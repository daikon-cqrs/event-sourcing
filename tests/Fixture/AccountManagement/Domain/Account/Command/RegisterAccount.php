<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command;

use Accordia\MessageBus\FromArrayTrait;
use Accordia\MessageBus\ToArrayTrait;
use Accordia\Cqrs\Aggregate\AggregateId;
use Accordia\Cqrs\Aggregate\Command;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;
use Accordia\Entity\ValueObject\Timestamp;

final class RegisterAccount extends Command
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Accordia\Entity\ValueObject\Timestamp
     * @buzz::fromArray->createFromString
     */
    private $expiresAt;

    /**
     * @var \Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole
     * @buzz::fromArray->fromNative
     */
    private $role;

    /**
     * @var \Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale
     * @buzz::fromArray->fromNative
     */
    private $locale;

    /**
     * @var \Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username
     * @buzz::fromArray->fromNative
     */
    private $username;

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
