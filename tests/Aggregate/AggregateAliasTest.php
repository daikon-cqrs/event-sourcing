<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\AggregateAlias;
use PHPUnit\Framework\TestCase;

final class AggregateAliasTest extends TestCase
{
    public function testFromNativeToNative()
    {
        $this->assertEquals(
            'testing.mock.pizza',
            AggregateAlias::fromNative('testing.mock.pizza')->toNative()
        );
    }

    public function testToString()
    {
        $this->assertEquals(
            'testing.mock.pizza',
            (string)AggregateAlias::fromNative('testing.mock.pizza')
        );
    }

    public function testEquals()
    {
        $aggregatePrefix = AggregateAlias::fromNative('testing.mock.pizza');
        $this->assertTrue($aggregatePrefix->equals(AggregateAlias::fromNative('testing.mock.pizza')));
        $this->assertFalse($aggregatePrefix->equals(AggregateAlias::fromNative('testing.mock.radish')));
    }

    public function testIsEmpty()
    {
        $this->assertFalse(AggregateAlias::fromNative('testing.mock.pizza')->isEmpty());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Creating empty aggregate aliases is not supported.
     */
    public function testMakeEmpty()
    {
        AggregateAlias::makeEmpty();
    } // @codeCoverageIgnore
}
