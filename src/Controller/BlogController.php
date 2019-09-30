<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog")
     * @Template("blog/index.html.twig")
     */
    public function index(BlogPostRepository $blogPostRepository)
    {
        return [
            'blogPosts' => $blogPostRepository->findAll()
        ];
    }
}
