<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\MessageBus\FromArrayTrait;
use Daikon\MessageBus\ToArrayTrait;
use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\DomainEvent;
use DaikonCqrsAggregateAggregateRevision;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\OauthServiceName;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\RandomToken;
use Daikon\Entity\ValueObject\Text;
use Daikon\Entity\ValueObject\Timestamp;
use Daikon\Entity\ValueObject\Uuid;

final class OauthTokenWasAdded extends DomainEvent
{
    use ToArrayTrait;
    use FromArrayTrait;

    /**
     * @var \Daikon\Entity\ValueObject\Uuid
     * @buzz::fromArray->fromNative
     */
    private $id;

    /**
     * @var \Daikon\Entity\ValueObject\Text
     * @buzz::fromArray->fromNative
     */
    private $tokenId;

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
     * @var \Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\OauthServiceName
     * @buzz::fromArray->fromNative
     */
    private $service;

    /**
     * @param RegisterOauthAccount $registration
     * @return OauthTokenWasAdded
     */
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

    /**
     * @return Text
     */
    public function getTokenId(): Text
    {
        return $this->tokenId;
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
     * @return OauthServiceName
     */
    public function getService(): OauthServiceName
    {
        return $this->service;
    }

    /**
     * @param Uuid $id
     * @param Text $tokenId
     * @param AggregateId $aggregateId
     * @param RandomToken $token
     * @param Timestamp $expiresAt
     * @param OauthServiceName $service
     * @param Revision|null $aggregateRevision
     */
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
