<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use DaikonCqrsAggregateAggregateRevision;
use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\DomainEvent;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class VerificationTokenWasAdded extends DomainEvent
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @MessageBus::deserialize(\Daikon\Entity\ValueObject\Uuid::fromNative)
     */
    private $id;

    /**
     * @MessageBus::deserialize(\Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken::fromNative)
     */
    private $token;

    /**
     * @param RegisterAccount $registerAccount
     * @return VerificationTokenWasAdded
     */
    public static function viaCommand(RegisterAccount $registerAccount): self
    {
        return new static(
            Uuid::generate(),
            $registerAccount->getAggregateId(),
            RandomToken::generate()
        );
    }

    public static function getAggregateRootClass(): string
    {
        return Account::class;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return RandomToken
     */
    public function getToken(): RandomToken
    {
        return $this->token;
    }

    /**
     * @param Uuid $id
     * @param AggregateId $aggregateId
     * @param RandomToken $token
     * @param Revision|null $aggregateRevision
     */
    protected function __construct(
        Uuid $id,
        AggregateId $aggregateId,
        RandomToken $token,
        Revision $aggregateRevision = null
    ) {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->id = $id;
        $this->token = $token;
    }
}
