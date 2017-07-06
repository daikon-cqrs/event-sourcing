<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use DaikonCqrsAggregateAggregateRevision;
use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\DomainEvent;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class PasswordTokenWasAdded extends DomainEvent
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
     * @MessageBus::deserialize(\Daikon\Entity\ValueObject\Timestamp::createFromString)
     */
    private $expiresAt;

    /**
     * @param RegisterAccount $registerAccount
     * @return PasswordTokenWasAdded
     */
    public static function viaCommand(RegisterAccount $registerAccount): self
    {
        return new static(
            Uuid::generate(),
            $registerAccount->getAggregateId(),
            RandomToken::generate(),
            $registerAccount->getExpiresAt()
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
     * @return Timestamp
     */
    public function getExpiresAt(): Timestamp
    {
        return $this->expiresAt;
    }

    /**
     * @param Uuid $id
     * @param AggregateId $aggregateId
     * @param RandomToken $token
     * @param Timestamp $expiresAt
     * @param Revision|null $aggregateRevision
     */
    protected function __construct(
        Uuid $id,
        AggregateId $aggregateId,
        RandomToken $token,
        Timestamp $expiresAt,
        Revision $aggregateRevision = null
    ) {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->id = $id;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }
}
