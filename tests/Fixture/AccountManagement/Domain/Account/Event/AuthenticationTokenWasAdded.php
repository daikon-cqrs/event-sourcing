<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\AggregateIdInterface;
use Daikon\Cqrs\Aggregate\AggregateRevision;
use Daikon\Cqrs\Aggregate\CommandInterface;
use Daikon\Cqrs\Aggregate\DomainEvent;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\MessageBus\MessageInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class AuthenticationTokenWasAdded extends DomainEvent
{
    /** @var Uuid */
    private $id;

    /** @var RandomToken */
    private $token;

    /** @var Timestamp */
    private $expiresAt;

    public static function viaCommand(CommandInterface $registration): self
    {
        // @todo check $registration instanceof RegiserAccount|RegisterOauthAccount
        /* @var RegisterAccount|RegisterOauthAccount $registration */
        return new static(
            Uuid::generate(),
            $registration->getAggregateId(),
            RandomToken::generate(),
            $registration->getExpiresAt()
        );
    }

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            Uuid::fromNative($nativeArray["id"]),
            AggregateId::fromNative($nativeArray["aggregateId"]),
            RandomToken::fromNative($nativeArray["token"]),
            Timestamp::fromNative($nativeArray["expiresAt"])
        );
    }

    public static function getAggregateRootClass(): string
    {
        return Account::class;
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
