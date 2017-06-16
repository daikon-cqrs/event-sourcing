<?php

namespace Accordia\Cqrs\Projection;

use Accordia\Cqrs\Aggregate\DomainEventInterface;
use Accordia\Entity\Entity\EntityInterface;

interface ProjectionInterface extends EntityInterface
{
    /**
     * @param DomainEventInterface $domainEvent
     * @return ProjectionInterface
     */
    public function project(DomainEventInterface $domainEvent): ProjectionInterface;
}
