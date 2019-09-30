<?php

namespace App\Command;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{
    protected static $defaultName = 'app:test';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var BlogPostRepository
     */
    private $blogPostRepository;

    public function __construct(
        string $name = null,
        EntityManagerInterface $entityManager,
        BlogPostRepository $blogPostRepository
    ) {
        parent::__construct($name);

        $this->blogPostRepository = $blogPostRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function create(string $title, string $content, string $status)
    {
        $blogPost = (new BlogPost())
            ->setTitle($title)
            ->setContent($content)
            ->setStatus($status)
        ;
        $this->entityManager->persist($blogPost);
        $this->entityManager->flush();

        return $blogPost;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $blogPost = $this->create(
            'Symfony Workflow is awesome !',
            'Read more at https://symfony.com/doc/current/workflow.html',
            'draft'
        );

        /*$blogPost = $this->blogPostRepository->find(1);
        $blogPost->setStatus('totooo');
        $this->entityManager->flush();*/

        dump($blogPost);
    }
}
