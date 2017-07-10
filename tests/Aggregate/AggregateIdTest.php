<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\EventSourcing\Aggregate\AggregateId;
use PHPUnit\Framework\TestCase;

final class AggregateIdTest extends TestCase
{
    public function testFromNativeToNativeRoundtrip()
    {
        $this->assertEquals(
            'testing.blog.article-123',
            AggregateId::fromNative('testing.blog.article-123')->toNative()
        );
    }

    public function testToString()
    {
        $this->assertEquals('testing.blog.article-123', (string)AggregateId::fromNative('testing.blog.article-123'));
    }

    public function testEquals()
    {
        $aggregateId1 = AggregateId::fromNative('testing.blog.article-123');
        $this->assertTrue($aggregateId1->equals(AggregateId::fromNative('testing.blog.article-123')));
        $this->assertFalse($aggregateId1->equals(AggregateId::fromNative('testing.blog.article-1234')));
    }

    public function testIsEmpty()
    {
        $this->assertFalse(AggregateId::fromNative('testing.blog.article-123')->isEmpty());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Creating empty aggregate-ids is not supported.
     */
    public function testMakeEmpty()
    {
        AggregateId::makeEmpty();
    }
}
