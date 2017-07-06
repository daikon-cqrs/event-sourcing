<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\Command;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\OauthServiceName;

final class RegisterOauthAccount extends Command
{
    private $expiresAt;

    private $service;

    private $tokenId;

    private $role;

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            AggregateId::fromNative($nativeArray["aggregateId"]),
            Text::fromNative($nativeArray["tokenId"]),
            OauthServiceName::fromNative($nativeArray["service"]),
            AccessRole::fromNative($nativeArray["role"]),
            Timestamp::createFromString($nativeArray["expiresAt"])
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

    public function getService(): Text
    {
        return $this->service;
    }

    public function getExpiresAt(): Timestamp
    {
        return $this->expiresAt;
    }

    public function getRole(): AccessRole
    {
        return $this->role;
    }

    public function toArray(): array
    {
        return array_merge([
            "expiresAt" => $this->expiresAt->toNative(),
            "role" => $this->role->toNative(),
            "service" => $this->service->toNative(),
            "tokenId" => $this->tokenId->toNative(),
        ], parent::toArray());
    }

    protected function __construct(
        AggregateId $aggregateId,
        Text $tokenId,
        OauthServiceName $service,
        AccessRole $role,
        Timestamp $expiresAt
    ) {
        parent::__construct($aggregateId);
        $this->tokenId = $tokenId;
        $this->service = $service;
        $this->role = $role;
        $this->expiresAt = $expiresAt;
    }
}
