<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\AggregatePrefix;
use Daikon\Tests\EventSourcing\Aggregate\Mock\Pizza;
use PHPUnit\Framework\TestCase;

final class AggregatePrefixTest extends TestCase
{
    public function testFromFqcn()
    {
        $prefix = AggregatePrefix::fromFqcn(Pizza::class);
        $this->markTestIncomplete("what to expect here?");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Creating empty aggregate prefixes is not supported.
     */
    public function testMakeEmpty()
    {
        AggregatePrefix::makeEmpty();
    }

    public function testFromNativeToNative()
    {
        $this->assertEquals(
            '\Testing\Domain\Blog\Article',
            AggregatePrefix::fromNative('\Testing\Domain\Blog\Article')->toNative()
        );
    }

    public function testToString()
    {
        $this->assertEquals(
            '\Testing\Domain\Blog\Article',
            (string)AggregatePrefix::fromNative('\Testing\Domain\Blog\Article')
        );
    }

    public function testEquals()
    {
        $aggregatePrefix = AggregatePrefix::fromNative('\Testing\Domain\Blog\Article');
        $this->assertTrue($aggregatePrefix->equals(AggregatePrefix::fromNative('\Testing\Domain\Blog\Article')));
        $this->assertFalse($aggregatePrefix->equals(AggregatePrefix::fromNative('\Testing\Domain\Blog\Comment')));
    }

    public function testIsEmpty()
    {
        $this->assertFalse(AggregatePrefix::fromNative('\Testing\Domain\Blog\Article')->isEmpty());
    }
}
