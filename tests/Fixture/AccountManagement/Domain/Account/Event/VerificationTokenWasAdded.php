<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\DomainEvent;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\MessageBus\MessageInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class VerificationTokenWasAdded extends DomainEvent
{
    private $id;

    private $token;

    public static function viaCommand(RegisterAccount $registerAccount): self
    {
        return new static(
            Uuid::generate(),
            $registerAccount->getAggregateId(),
            RandomToken::generate()
        );
    }

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            Uuid::fromNative($nativeArray["id"]),
            AggregateId::fromNative($nativeArray["aggregateId"]),
            RandomToken::fromNative($nativeArray["token"])
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

    public function toArray(): array
    {
        return array_merge([
            "id" => $this->id->toNative(),
            "token" => $this->token->toNative(),
        ], parent::toArray());
    }

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
