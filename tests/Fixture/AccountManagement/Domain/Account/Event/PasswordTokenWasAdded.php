<?php

namespace Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\DomainEvent;
use Daikon\EventSourcing\Aggregate\DomainEventInterface;
use Daikon\MessageBus\MessageInterface;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class PasswordTokenWasAdded extends DomainEvent
{
    /** @var Uuid */
    private $id;

    /** @var RandomToken */
    private $token;

    /** @var Timestamp */
    private $expiresAt;

    public static function viaCommand(RegisterAccount $registerAccount): self
    {
        return new static(
            Uuid::generate(),
            $registerAccount->getAggregateId(),
            RandomToken::generate(),
            $registerAccount->getExpiresAt()
        );
    }

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            Uuid::fromNative($nativeArray["id"]),
            AggregateId::fromNative($nativeArray["aggregateId"]),
            RandomToken::fromNative($nativeArray["token"]),
            Timestamp::createFromString($nativeArray["expiresAt"])
        );
    }

    public static function getAggregateRootClass(): string
    {
        return Account::class;
    }

    public function conflictsWith(DomainEventInterface $otherEvent): bool
    {
        return false;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getToken(): RandomToken
    {
        return $this->token;
    }

    public function getExpiresAt(): Timestamp
    {
        return $this->expiresAt;
    }

    public function toArray(): array
    {
        return array_merge([
            "expiresAt" => $this->expiresAt->toNative(),
            "id" => $this->id->toNative(),
            "token" => $this->token->toNative()
        ], parent::toArray());
    }

    protected function __construct(
        Uuid $id,
        AggregateIdInterface $aggregateId,
        RandomToken $token,
        Timestamp $expiresAt,
        AggregateRevision $aggregateRevision = null
    ) {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->id = $id;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }
}
