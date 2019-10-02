<?php

namespace App\EventListener;

use App\Entity\BlogPost;
use App\Services\WorkflowChecker;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\WorkflowInterface;

class BlogPostACL
{
    /**
     * @var WorkflowChecker
     */
    private $workflowChecker;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var WorkflowInterface
     */
    private $blogPublishingStateMachine;

    public function __construct(
        WorkflowChecker $workflowChecker,
        Registry $registry,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->registry = $registry;
        $this->workflowChecker = $workflowChecker;
    }

    public function preUpdate(BlogPost $blogPost, PreUpdateEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $entity = $args->getEntity();
        $changeSet = $unitOfWork->getEntityChangeSet($entity);

        dump($this->workflowChecker->check($entity, $changeSet));
    }

    public function prePersist(BlogPost $blogPost, LifecycleEventArgs $args)
    {
        // dump($blogPost->getStatus());
        // dump($this->blogPublishingStateMachine->can($blogPost, 'to_review'));
        // die('prePersist');
    }
}
