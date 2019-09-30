<?php

namespace App\EventListener;

use App\Entity\BlogPost;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Workflow\WorkflowInterface;

class BlogPostACL
{
    /**
     * @var WorkflowInterface
     */
    private $blogPublishingStateMachine;

    public function __construct(WorkflowInterface $blogPublishingStateMachine)
    {
        $this->blogPublishingStateMachine = $blogPublishingStateMachine;
    }

    public function preUpdate(BlogPost $blogPost, PreUpdateEventArgs $args)
    {
        // dump($this->blogPublishingStateMachine->can($blogPost, 'publish'));
        // die('preUpdate');
    }

    public function prePersist(BlogPost $blogPost, LifecycleEventArgs $args)
    {
        // dump($blogPost->getStatus());
        // dump($this->blogPublishingStateMachine->can($blogPost, 'to_review'));
        // die('prePersist');
    }
}
