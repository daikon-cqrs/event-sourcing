<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\AggregateRevision;
use PHPUnit\Framework\TestCase;

final class AggregateRevisionTest extends TestCase
{
    public function testMakeEmpty()
    {
        $revision = AggregateRevision::makeEmpty();
        $this->assertEquals(0, $revision->toNative());
    }

    public function testFromNativeToNative()
    {
        $this->assertEquals(42, AggregateRevision::fromNative(42)->toNative());
    }

    public function testToString()
    {
        $this->assertEquals('42', (string)AggregateRevision::fromNative(42));
    }

    public function testEquals()
    {
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->equals(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->equals(AggregateRevision::fromNative(23)));
    }

    public function testIsInitial()
    {
        $this->assertTrue(AggregateRevision::fromNative(1)->isInitial());
        $this->assertFalse(AggregateRevision::fromNative(0)->isInitial());
        $this->assertFalse(AggregateRevision::fromNative(42)->isInitial());
    }

    public function testIsGreaterThan()
    {
        /** @var $revision AggregateRevision */
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->isGreaterThan(AggregateRevision::fromNative(41)));
        $this->assertFalse($revision->isGreaterThan(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->isGreaterThan(AggregateRevision::fromNative(43)));
    }

    public function testIsGreaterThanOrEqual()
    {
        /** @var $revision AggregateRevision */
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->isGreaterThanOrEqual(AggregateRevision::fromNative(41)));
        $this->assertTrue($revision->isGreaterThanOrEqual(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->isGreaterThanOrEqual(AggregateRevision::fromNative(43)));
    }

    public function testIsLessThan()
    {
        /** @var $revision AggregateRevision */
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->isLessThan(AggregateRevision::fromNative(43)));
        $this->assertFalse($revision->isLessThan(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->isLessThan(AggregateRevision::fromNative(41)));
    }

    public function testIsLessThanOrEqual()
    {
        /** @var $revision AggregateRevision */
        $revision = AggregateRevision::fromNative(42);
        $this->assertTrue($revision->isLessThanOrEqual(AggregateRevision::fromNative(43)));
        $this->assertTrue($revision->isLessThanOrEqual(AggregateRevision::fromNative(42)));
        $this->assertFalse($revision->isLessThanOrEqual(AggregateRevision::fromNative(41)));
    }

    public function testIsWithinRange()
    {
        /** @var $revision AggregateRevision */
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

    public function testMakeFromNonInteger()
    {
        $this->expectException(\TypeError::class);
        AggregateRevision::fromNative('not an int');
    } // @codeCoverageIgnore
}
