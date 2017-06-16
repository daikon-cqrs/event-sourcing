<?php

namespace Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command;

use Accordia\MessageBus\FromArrayTrait;
use Accordia\MessageBus\ToArrayTrait;
use Accordia\Cqrs\Aggregate\AggregateId;
use Accordia\Cqrs\Aggregate\Command;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;
use Accordia\Entity\ValueObject\Text;
use Accordia\Entity\ValueObject\Timestamp;

final class RegisterOauthAccount extends Command
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Accordia\Entity\ValueObject\Timestamp
     * @buzz::fromArray->createFromString
     */
    private $expiresAt;

    /**
     * @var \Accordia\Entity\ValueObject\Text
     * @buzz::fromArray->fromNative
     */
    private $service;

    /**
     * @var \Accordia\Entity\ValueObject\Text
     * @buzz::fromArray->fromNative
     */
    private $tokenId;

    /**
     * @var \Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole
     * @buzz::fromArray->fromNative
     */
    private $role;

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
        Timestamp $expiresAt)
    {
        parent::__construct($aggregateId);
        $this->tokenId = $tokenId;
        $this->service = $service;
        $this->role = $role;
        $this->expiresAt = $expiresAt;
    }
}
