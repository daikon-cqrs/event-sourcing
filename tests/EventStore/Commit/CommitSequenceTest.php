<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventStore\Commit;

use Daikon\EventSourcing\EventStore\Commit\CommitSequence;
use Daikon\EventSourcing\EventStore\Stream\Sequence;
use PHPUnit\Framework\TestCase;

final class CommitSequenceTest extends TestCase
{
    public function testMakeEmpty(): void
    {
        $commitSequence = CommitSequence::makeEmpty();
        $this->assertEquals([], $commitSequence->toNative());
    }

    public function testHas(): void
    {
        $sequence = Sequence::fromNative(2);
        $commitSequence = CommitSequence::makeEmpty();
        $this->assertFalse($commitSequence->has($sequence));
    }
}
