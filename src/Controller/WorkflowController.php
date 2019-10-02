<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Services\Test;
use App\Services\TestDecorated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

class WorkflowController extends AbstractController
{
    const MAPPING = [
        'blog_publishing' => BlogPost::class
    ];

    /**
     * @Route("/workflow/{name}/{id}/{transition}", name="worflow_apply")
     */
    public function apply(
        Request $request,
        EntityManagerInterface $manager,
        Registry $registry,
        string $name,
        int $id,
        string $transition
    ) {
        $entityClass = self::MAPPING[$name];
        $repository = $manager->getRepository($entityClass);
        $entity = $repository->find($id);
        $workflow = $registry->get($entity, $name);
        $workflow->apply($entity, $transition);
        $manager->flush();

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/workflow/{name}/{id}", name="worflows")
     */
    public function transitions(EntityManagerInterface $manager, Registry $registry, string $name, int $id)
    {
        $entityClass = self::MAPPING[$name];
        $repository = $manager->getRepository($entityClass);
        $entity = $repository->find($id);
        $workflow = $registry->get($entity, $name);
        $transitions = $workflow->getEnabledTransitions($entity);

        return new JsonResponse([
            'entityClass' => $entityClass,
            'entityId' => $id,
            'transitions' => $transitions
        ]);
    }
}
