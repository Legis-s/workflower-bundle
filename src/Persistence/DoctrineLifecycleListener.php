<?php
/*
 * Copyright (c) KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsWorkflowerBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\WorkflowerBundle\Persistence;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPMentors\Workflower\Persistence\WorkflowSerializableInterface;
use PHPMentors\Workflower\Persistence\WorkflowSerializerInterface;

class DoctrineLifecycleListener
{
    /**
     * @var WorkflowSerializerInterface
     */
    private $workflowSerializer;

    /**
     * @param WorkflowSerializerInterface $workflowSerializer
     */
    public function __construct(WorkflowSerializerInterface $workflowSerializer)
    {
        $this->workflowSerializer = $workflowSerializer;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof WorkflowSerializableInterface && $entity->getWorkflow() !== null) {
            $entity->setSerializedWorkflow($this->workflowSerializer->serialize($entity->getWorkflow()));
        }
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof WorkflowSerializableInterface && $entity->getWorkflow() !== null) {
            $entity->setSerializedWorkflow($this->workflowSerializer->serialize($entity->getWorkflow()));
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof WorkflowSerializableInterface && $entity->getSerializedWorkflow() !== null) {
            $entity->setWorkflow($this->workflowSerializer->deserialize($entity->getSerializedWorkflow()));
        }
    }
}
