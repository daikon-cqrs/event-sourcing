<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\DomainEvent;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;
use Daikon\MessageBus\MessageInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\OauthServiceName;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;

final class OauthTokenWasAdded extends DomainEvent
{
    private $id;

    private $tokenId;

    private $token;

    private $expiresAt;

    private $service;

    public static function viaCommand(RegisterOauthAccount $registration): self
    {
        return new static(
            Uuid::generate(),
            $registration->getTokenId(),
            $registration->getAggregateId(),
            RandomToken::generate(),
            $registration->getExpiresAt(),
            $registration->getService()
        );
    }

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            Uuid::fromNative($nativeArray["id"]),
            Text::fromNative($nativeArray["tokenId"]),
            AggregateId::fromNative($nativeArray["aggregateId"]),
            RandomToken::fromNative($nativeArray["token"]),
            Timestamp::createFromString($nativeArray["expiresAt"]),
            OauthServiceName::fromNative($nativeArray["service"])
        );
    }

    public static function getAggregateRootClass(): string
    {
        return Account::class;
    }

    public function getTokenId(): Text
    {
        return $this->tokenId;
    }

    public function getToken(): RandomToken
    {
        return $this->token;
    }

    public function getExpiresAt(): Timestamp
    {
        return $this->expiresAt;
    }

    public function getService(): OauthServiceName
    {
        return $this->service;
    }

    public function toArray(): array
    {
        return array_merge([
            "expiresAt" => $this->expiresAt->toNative(),
            "tokenId" => $this->tokenId->toNative(),
            "id" => $this->id->toNative(),
            "service" => $this->service->toNative(),
            "token" => $this->token->toNative(),
        ], parent::toArray());
    }

    protected function __construct(
        Uuid $id,
        Text $tokenId,
        AggregateId $aggregateId,
        RandomToken $token,
        Timestamp $expiresAt,
        OauthServiceName $service,
        Revision $aggregateRevision = null
    ) {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->id = $id;
        $this->tokenId = $tokenId;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->service = $service;
    }
}
