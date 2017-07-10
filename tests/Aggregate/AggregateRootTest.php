<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\Tests\EventSourcing\Aggregate\Mock\BakePizza;
use Daikon\Tests\EventSourcing\Aggregate\Mock\Pizza;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function testStartAggregateRootLifecycle()
    {
        /** @var $bakePizza BakePizza */
        $bakePizza = BakePizza::fromArray([
            'aggregateId' => 'pizza-42-6-23',
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ]);
        $pizza = Pizza::bake($bakePizza);

        $this->assertEquals('pizza-42-6-23', $pizza->getIdentifier());
        $this->assertEquals(1, $pizza->getRevision()->toNative());
        $this->assertCount(1, $pizza->getTrackedEvents());
    }
}
