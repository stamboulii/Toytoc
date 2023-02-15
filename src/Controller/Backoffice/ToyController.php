<?php

namespace App\Controller\Backoffice;
use App\Entity\Category;
use App\Entity\Toy;
use App\Form\Backoffice\Toy\ToyFilterType;
use App\Form\Backoffice\Toy\ToyType;
use App\Helper\HttpQueryHelper;
use App\Repository\CategoryRepository;
use App\Repository\ToyRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/toy', name: 'app_backoffice_toy_')]
class ToyController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(ToyRepository $toyRepository, Request $request): Response
    {
        $filters = ['toy' => $toyRepository->findAll()];
        $form = $this->createForm(ToyFilterType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }
        return $this->render('backoffice/Toys/toys.html.twig', [
            'form' => $form->createView(),
            'toy' => $toyRepository->getToyByFiltersAndPaginator(
                $filters,
                HttpQueryHelper::getOrderBy($request),
                HttpQueryHelper::getLimit($request),
                HttpQueryHelper::getOffset($request),
            )
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ToyRepository $toyRepository): Response
    {
        $toy = new Toy();
        $form = $this->createForm(ToyType::class, $toy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $toyRepository->save($toy, true);

            return $this->redirectToRoute('app_backoffice_toy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/Toys/new.html.twig', [
            'toy' => $toy,
            'form' => $form,

        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Toy $toy, ToyRepository $toyRepository): Response
    {
        $form = $this->createForm(ToyType::class, $toy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $toyRepository->save($toy, true);

            return $this->redirectToRoute('app_backoffice_toy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/Toys/edit.html.twig', [
            'toy' => $toy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Toy $toy, ToyRepository $toyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$toy->getId(), $request->request->get('_token'))) {
            $toyRepository->remove($toy, true);
        }

        return $this->redirectToRoute('app_backoffice_toy_index', [], Response::HTTP_SEE_OTHER);
    }

//    public function show(ManagerRegistry $doctrine, int $id): Response
//    {
//        $toy = $doctrine->getRepository(Toy::class)->findOneByIdJoinedToCategory($id);
//
//        $user = $toy->getUser();
//
//        return $this->;
//    }
}