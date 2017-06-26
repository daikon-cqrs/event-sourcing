<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\DomainEvent;
use DaikonCqrsAggregateAggregateRevision;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Locale;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username;

final class AccountWasRegistered extends DomainEvent
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\Username
     * @buzz::fromArray->fromNative
     */
    private $username;

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
