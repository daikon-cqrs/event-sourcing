<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\DomainEvent;
use DaikonCqrsAggregateAggregateRevision;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Daikon\Entity\ValueObject\Uuid;

final class VerificationTokenWasAdded extends DomainEvent
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
