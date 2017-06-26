<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\CommandInterface;
use Daikon\Cqrs\Aggregate\DomainEvent;
use DaikonCqrsAggregateAggregateRevision;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;

final class AuthenticationTokenWasAdded extends DomainEvent
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Daikon\Entity\ValueObject\Uuid
     * @buzz::fromArray->fromNative
     */
    private $id;

    /**
     * @var \Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken
     * @buzz::fromArray->fromNative
     */
    private $token;

    /**
     * @var \Daikon\Entity\ValueObject\Timestamp
     * @buzz::fromArray->createFromString
     */
    private $expiresAt;

    /**
     * @param CommandInterface $registration
     * @return AuthenticationTokenWasAdded
     */
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
