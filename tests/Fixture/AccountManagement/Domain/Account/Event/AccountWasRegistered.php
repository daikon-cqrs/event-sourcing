<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Accordia\MessageBus\FromArrayTrait;
use Accordia\MessageBus\ToArrayTrait;
use Accordia\Cqrs\Aggregate\AggregateId;
use Accordia\Cqrs\Aggregate\DomainEvent;
use Accordia\Cqrs\Aggregate\Revision;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;

final class AccountWasRegistered extends DomainEvent
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username
     * @buzz::fromArray->fromNative
     */
    private $username;

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
     * @param  RegisterAccount $registerAccount
     * @return AccountWasRegistered
     */
    public static function viaCommand(RegisterAccount $registerAccount): self
    {
        return new static(
            $registerAccount->getAggregateId(),
            $registerAccount->getRole(),
            $registerAccount->getUsername(),
            $registerAccount->getLocale()
        );
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
     * @param Revision|null $aggregateRevision
     */
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
