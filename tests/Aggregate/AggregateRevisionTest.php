<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AggregateRevisionTest extends TestCase
{
    public function testMakeEmpty(): void
    {
        $revision = AggregateRevision::makeEmpty();
        $this->assertEquals(0, $revision->toNative());
    }

    public function testFromNativeToNative(): void
    {
        $this->assertEquals(42, AggregateRevision::fromNative(42)->toNative());
    }

    public function testFromStringToNative(): void
    {
        $this->assertEquals(42, AggregateRevision::fromNative('42')->toNative());
    }

    public function testToString(): void
    {
        $this->assertEquals('42', (string)AggregateRevision::fromNative(42));
    }

    public function testEquals(): void
    {
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->equals(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->equals(AggregateRevision::fromNative(23)));
    }

    public function testIsInitial(): void
    {
        $this->assertTrue(AggregateRevision::fromNative(1)->isInitial());
        $this->assertFalse(AggregateRevision::fromNative(0)->isInitial());
        $this->assertFalse(AggregateRevision::fromNative(42)->isInitial());
    }

    public function testIsGreaterThan(): void
    {
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->isGreaterThan(AggregateRevision::fromNative(41)));
        $this->assertFalse($revision->isGreaterThan(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->isGreaterThan(AggregateRevision::fromNative(43)));
    }

    public function testIsGreaterThanOrEqual(): void
    {
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->isGreaterThanOrEqual(AggregateRevision::fromNative(41)));
        $this->assertTrue($revision->isGreaterThanOrEqual(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->isGreaterThanOrEqual(AggregateRevision::fromNative(43)));
    }

    public function testIsLessThan(): void
    {
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->isLessThan(AggregateRevision::fromNative(43)));
        $this->assertFalse($revision->isLessThan(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->isLessThan(AggregateRevision::fromNative(41)));
    }

    public function testIsLessThanOrEqual(): void
    {
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->isLessThanOrEqual(AggregateRevision::fromNative(43)));
        $this->assertTrue($revision->isLessThanOrEqual(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->isLessThanOrEqual(AggregateRevision::fromNative(41)));
    }

    public function testIsWithinRange(): void
    {
        $revision = AggregateRevision::fromNative(42);

        $this->assertTrue($revision->isWithinRange(
            AggregateRevision::fromNative(40),
            AggregateRevision::fromNative(43)
        ));
        $this->assertFalse($revision->isWithinRange(
            AggregateRevision::fromNative(23),
            AggregateRevision::fromNative(30)
        ));
    }

    public function testMakeFromInvalidIntegerish(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AggregateRevision::fromNative('what');
    } // @codeCoverageIgnore
}
