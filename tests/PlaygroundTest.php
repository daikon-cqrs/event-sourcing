<?php

namespace Accordia\Tests\Cqrs;

use AccordiaCqrsAggregateAggregateRevision;
use Accordia\Cqrs\Aggregate\AggregateId;
use Accordia\Cqrs\Aggregate\AggregateRootInterface;
use Accordia\Cqrs\EventStore\Commit;
use Accordia\Cqrs\EventStore\CommitStream;
use Accordia\Cqrs\EventStore\PersistenceAdapterInterface;
use Accordia\Cqrs\EventStore\CommitStreamRevision;
use Accordia\Cqrs\EventStore\UnitOfWork;
use Accordia\Cqrs\Projection\StandardProjector;
use Accordia\MessageBus\Channel\Channel;
use Accordia\MessageBus\Channel\ChannelMap;
use Accordia\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerList;
use Accordia\MessageBus\Channel\Subscription\Subscription;
use Accordia\MessageBus\Channel\Subscription\SubscriptionMap;
use Accordia\MessageBus\Channel\Subscription\Transport\InProcessTransport;
use Accordia\MessageBus\Envelope;
use Accordia\MessageBus\MessageBus;
use Accordia\MessageBus\MessageBusInterface;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\CommandHandler\RegisterAccountHandler;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Entity\AccountEntityType;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AccountWasRegistered;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\AuthenticationTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event\VerificationTokenWasAdded;
use Accordia\Tests\Cqrs\Fixture\AccountManagement\Projection\AccountProjectionType;
use Accordia\Tests\Cqrs\Fixture\LazyHandler;
use Accordia\Tests\Cqrs\Fixture\NoOpHandler;
use PHPUnit\Framework\TestCase;

final class PlaygroundTest extends TestCase
{
    const ACCOUNT_ID = "account-23";

    const ROLE = "admin";

    public function testCommandToProjectionRoundtripOnRealBus()
    {
        $registerCommand = $this->createCommand();
        $this->assertTrue($this->setupMessageBus()->publish($registerCommand, "commands"));
    }

    public function testRegister()
    {
        $registerCommand = $this->createCommand();
        $account = Account::register($registerCommand, new AccountEntityType);
        $this->assertInstanceOf(AggregateRootInterface::CLASS, $account);
        $this->assertEquals(3, $account->getRevision()->toNative());
    }

    public function testCommitRoundtrip()
    {
        $expectedMessages = $this->getMessageExpectations();
        $messageBusMock = $this->getMockBuilder(MessageBusInterface::class)
            ->setMethods([ "publish", "receive" ])
            ->getMock();
        $messageBusMock->expects($this->exactly(count($expectedMessages)))
            ->method("publish")
            ->withConsecutive(...$expectedMessages)
            ->willReturn(true);

        $persistenceMock = $this->getMockBuilder(PersistenceAdapterInterface::class)
            ->setMethods([ "storeStream", "loadStream" ])
            ->getMock();
        $persistenceMock->expects($this->once())
            ->method("storeStream")
            ->with($this->callback(function (CommitStream $commitStream) {
                $this->assertEquals($commitStream->getStreamRevision()->toNative(), 1);
                $this->assertEquals($commitStream->getAggregateRevision()->toNative(), 3);
                return true;
            }), CommitStreamRevision::makeEmpty())
            ->willReturn(true);

        $handler = new RegisterAccountHandler(
            new AccountEntityType,
            new UnitOfWork(Account::class, $persistenceMock),
            $messageBusMock
        );
        $registerCommand = $this->createCommand();
        $this->assertTrue($handler->handle(Envelope::wrap($registerCommand)));
    }

    private function getMessageExpectations()
    {
        $accountId = AggregateId::fromNative(self::ACCOUNT_ID);
        return [
            [ $this->callback(function (Commit $commit) {
                $this->assertCount(3, $commit->getEventLog());
                return true;
            }), "commits" ],
            [ $this->callback(function (AccountWasRegistered $event) use ($accountId) {
                $this->assertTrue($event->getAggregateId()->equals($accountId));
                return true;
            }), "events" ],
            [ $this->callback(function (AuthenticationTokenWasAdded $event) use ($accountId) {
                $this->assertTrue($event->getAggregateId()->equals($accountId));
                return true;
            }), "events" ],
            [ $this->callback(function (VerificationTokenWasAdded $event) use ($accountId) {
                $this->assertTrue($event->getAggregateId()->equals($accountId));
                return true;
            }), "events" ]
        ];
    }

    private function setupMessageBus()
    {
        $messageBus = false;
        $persistenceMock = $this->createMock(PersistenceAdapterInterface::class);
        $persistenceMock->method("storeStream")->willReturn(true);
        $registerAccountFactory = function () use (&$messageBus, $persistenceMock) {
            return new RegisterAccountHandler(
                new AccountEntityType,
                new UnitOfWork(Account::class, $persistenceMock),
                $messageBus
            );
        };
        $inProc = new InProcessTransport("inproc");
        $commandHandlers = new MessageHandlerList([ new LazyHandler($registerAccountFactory) ]);
        $commandSub = new Subscription("command-sub", $inProc, $commandHandlers);
        $commandChannel = new Channel("commands", new SubscriptionMap([ $commandSub ]));

        $commitHandlers = new MessageHandlerList([ new StandardProjector(new AccountProjectionType) ]);
        $commitSub = new Subscription("commit-sub", $inProc, $commitHandlers);
        $commitChannel = new Channel("commits", new SubscriptionMap([ $commitSub ]));

        $eventHandlers = new MessageHandlerList([ new NoOpHandler ]);
        $eventSub = new Subscription("event-sub", $inProc, $eventHandlers);
        $eventChannel = new Channel("events", new SubscriptionMap([ $eventSub ]));

        $messageBus = new MessageBus(new ChannelMap([ $commandChannel, $commitChannel, $eventChannel ]));
        return $messageBus;
    }

    private function createCommand()
    {
        return RegisterAccount::fromArray([
            "aggregateId" => self::ACCOUNT_ID,
            "role" => self::ROLE,
            "username" => "sheila",
            "locale" => "en_US",
            "expiresAt" => (new \DatetimeImmutable)->modify("+2hours")->format("Y-m-d\TH:i:s.uP")
        ]);
    }
}
