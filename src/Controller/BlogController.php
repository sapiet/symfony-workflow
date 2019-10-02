<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use App\Services\WorkflowChecker;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog")
     * @Template("blog/index.html.twig")
     */
    public function index(WorkflowChecker $workflowChecker, BlogPostRepository $blogPostRepository)
    {
        return [
            'blogPosts' => array_map(function(BlogPost $blogPost) use ($workflowChecker) {
                return [
                    'entity' => $blogPost,
                    'statuses' => $workflowChecker->getStateMachine($blogPost, 'status')->getDefinition()->getPlaces()
                ];
            }, $blogPostRepository->findAll())
        ];
    }

    /**
     * @Route("update-status", name="update_status", methods={"POST"})
     */
    public function updateStatus(
        Request $request,
        EntityManagerInterface $entityManager,
        BlogPostRepository $blogPostRepository
    ) {
        $blogPost = $blogPostRepository->find($request->request->get('id'));
        $blogPost->setStatus($request->request->get('status'));
        $entityManager->flush();

        return $this->redirectToRoute('blog');
    }
}
