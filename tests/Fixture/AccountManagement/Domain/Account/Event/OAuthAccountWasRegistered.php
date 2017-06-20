<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Accordia\MessageBus\FromArrayTrait;
use Accordia\MessageBus\ToArrayTrait;
use Accordia\Cqrs\Aggregate\AggregateId;
use Accordia\Cqrs\Aggregate\DomainEvent;
use AccordiaCqrsAggregateAggregateRevision;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;

final class OauthAccountWasRegistered extends DomainEvent
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole
     * @buzz::fromArray->fromNative
     */
    private $role;

    /**
     * @param RegisterOauthAccount $registration
     * @return OauthAccountWasRegistered
     */
    public static function viaCommand(RegisterOauthAccount $registration): self
    {
        return new static($registration->getAggregateId(), $registration->getRole());
    }

    /**
     * @return AccessRole
     */
    public function getRole(): AccessRole
    {
        return $this->role;
    }

    /**
     * @param AggregateId $aggregateId
     * @param AccessRole $role
     * @param Revision|null $aggregateRevision
     */
    protected function __construct(AggregateId $aggregateId, AccessRole $role, Revision $aggregateRevision = null)
    {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->role = $role;
    }
}
