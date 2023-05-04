<?php

namespace App\Controller\Frontoffice;

use App\Helper\HttpQueryHelper;
use App\Repository\CategoryRepository;
use App\Repository\ToyRepository;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Toy;
use App\Form\Backoffice\Toy\ToyType;



class FrontController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('frontoffice/front/index.html.twig', [
            'categories' => $categoryRepository->getHomepageCategories(),
        ]);
    }

    #[Route('/toys/{id}', name: 'app_frontoffice_toys', methods: ['GET'])]
    public function toys(Category $category, ToyRepository $toyRepository, Request $request): Response
    {
        $filters = $toyRepository->getToysByCategory($category);

        $form    = $this->createForm(ToyType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }



        return $this->render('frontoffice/toys/listetoys.html.twig', [
            'form' => $form->createView(),
            'toys' => $filters,
        ]);
    }

    #[Route('/detailtoy/{id}', name: 'app_frontoffice_detailtoy')]
    public function detailtoy(Request $request, Toy $toy): Response
    {
        $form= $this->createForm(ToyType::class)->handleRequest($request);
        return $this->render('frontoffice/toys/detailtoy.html.twig', [
            'toy'  => $toy,
            'form' => $form->createView(),
        ]);
    }
}
