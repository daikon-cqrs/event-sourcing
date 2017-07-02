<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command;

use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\Command;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\Timestamp;

final class RegisterOauthAccount extends Command
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Daikon\Entity\ValueObject\Timestamp
     * @buzz::fromArray->createFromString
     */
    private $expiresAt;

    /**
     * @var \Daikon\Entity\ValueObject\Text
     * @buzz::fromArray->fromNative
     */
    private $service;

    /**
     * @var \Daikon\Entity\ValueObject\Text
     * @buzz::fromArray->fromNative
     */
    private $tokenId;

    /**
     * @var \Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole
     * @buzz::fromArray->fromNative
     */
    private $role;

    public static function getAggregateRootClass(): string
    {
        return Account::class;
    }

    /**
     * @return Text
     */
    public function getTokenId(): Text
    {
        return $this->tokenId;
    }

    /**
     * @return Text
     */
    public function getService(): Text
    {
        return $this->service;
    }

    /**
     * @return Timestamp
     */
    public function getExpiresAt(): Timestamp
    {
        return $this->expiresAt;
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
     * @param Text $tokenId
     * @param Text $service
     * @param AccessRole $role
     * @param Timestamp $expiresAt
     */
    protected function __construct(
        AggregateId $aggregateId,
        Text $tokenId,
        Text $service,
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
