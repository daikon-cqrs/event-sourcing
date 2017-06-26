<?php

namespace Daikon\Cqrs\Projection;

use Daikon\Cqrs\Aggregate\DomainEventInterface;
use Daikon\Entity\Entity\EntityInterface;

interface ProjectionInterface extends EntityInterface
{
    /**
     * @param DomainEventInterface $domainEvent
     * @return ProjectionInterface
     */
    public function project(DomainEventInterface $domainEvent): ProjectionInterface;
}
