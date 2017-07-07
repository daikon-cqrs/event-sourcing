<?php

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateRootInterface;
use Daikon\EventSourcing\EventStore\Commit;
use Daikon\EventSourcing\EventStore\Stream;
use Daikon\EventSourcing\EventStore\StreamRevision;
use Daikon\EventSourcing\EventStore\StoreSuccess;
use Daikon\EventSourcing\EventStore\StreamStoreInterface;
use Daikon\EventSourcing\EventStore\UnitOfWork;
use Daikon\MessageBus\Channel\Channel;
use Daikon\MessageBus\Channel\ChannelMap;
use Daikon\MessageBus\Channel\Subscription\MessageHandler\MessageHandlerList;
use Daikon\MessageBus\Channel\Subscription\Subscription;
use Daikon\MessageBus\Channel\Subscription\SubscriptionMap;
use Daikon\MessageBus\Channel\Subscription\Transport\InProcessTransport;
use Daikon\MessageBus\Envelope;
use Daikon\MessageBus\MessageBus;
use Daikon\MessageBus\MessageBusInterface;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\CommandHandler\RegisterAccountHandler;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Command\RegisterAccount;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\AccountWasRegistered;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\AuthenticationTokenWasAdded;
use Daikon\Tests\EventSourcing\Fixture\AccountManagement\Domain\Account\Event\VerificationTokenWasAdded;
use Daikon\Tests\EventSourcing\Fixture\LazyHandler;
use Daikon\Tests\EventSourcing\Fixture\NoOpHandler;
use PHPUnit\Framework\TestCase;

final class PlaygroundTest extends TestCase
{
    const ACCOUNT_ID = "account-23";

    const ROLE = "admin";

    public function testCommandToProjectionRoundtripOnRealBus()
    {
        $registerCommand = $this->createCommand();
        // no commit handler register down in the setup routing, so bus will yield false
        $this->assertFalse($this->setupMessageBus()->publish($registerCommand, "commands"));
    }

    public function testRegister()
    {
        $registerCommand = $this->createCommand();
        $account = Account::register($registerCommand);
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

        $streamStoreMock = $this->getMockBuilder(StreamStoreInterface::class)
            ->setMethods([ "commit", "checkout" ])
            ->getMock();
        $streamStoreMock->expects($this->once())
            ->method("commit")
            ->with($this->callback(function (Stream $commitStream) {
                $this->assertEquals($commitStream->getAggregateRevision()->toNative(), 3);
                return true;
            }), StreamRevision::makeEmpty())
            ->willReturn(new StoreSuccess);

        $handler = new RegisterAccountHandler(
            new UnitOfWork(Account::class, $streamStoreMock),
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
        $messageBus = null;
        $streamStoreMock = $this->createMock(StreamStoreInterface::class);
        $streamStoreMock->method("commit")->willReturn(new StoreSuccess);
        $registerAccountFactory = function () use (&$messageBus, $streamStoreMock) {
            return new RegisterAccountHandler(
                new UnitOfWork(Account::class, $streamStoreMock),
                $messageBus
            );
        };
        $inProc = new InProcessTransport("inproc");
        $commandHandlers = new MessageHandlerList([ new LazyHandler($registerAccountFactory) ]);
        $commandSub = new Subscription("command-sub", $inProc, $commandHandlers);
        $commandChannel = new Channel("commands", new SubscriptionMap([ $commandSub ]));

        $commitSub = new Subscription("commit-sub", $inProc, new MessageHandlerList);
        $commitChannel = new Channel("commits", new SubscriptionMap([ $commitSub ]));

        $eventHandlers = new MessageHandlerList([ new NoOpHandler ]);
        $eventSub = new Subscription("event-sub", $inProc, $eventHandlers);
        $eventChannel = new Channel("events", new SubscriptionMap([ $eventSub ]));

        $messageBus = new MessageBus(new ChannelMap([ $commandChannel, $commitChannel, $eventChannel ]));
        return $messageBus;
    }

    private function createCommand(): RegisterAccount
    {
        /** @var $registerAccount RegisterAccount */
        $registerAccount = RegisterAccount::fromArray([
            "aggregateId" => self::ACCOUNT_ID,
            "role" => self::ROLE,
            "username" => "sheila",
            "locale" => "en_US",
            "expiresAt" => (new \DatetimeImmutable)->modify("+2hours")->format("Y-m-d\TH:i:s.uP")
        ]);
        return $registerAccount;
    }
}
