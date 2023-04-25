<?php

namespace App\Controller\Frontoffice;

use App\Helper\HttpQueryHelper;
use App\Repository\CategoryRepository;
use App\Repository\ToyRepository;
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

    #[Route('/toys',name:'app_frontoffice_toys',methods: ['GET'])]
    public function toys(ToyRepository $toyRepository, Request $request): Response
    {
        $filters = [
            'user_id'     => $request->query->get('user_id'),
            'category_id' => $request->query->get('category_id'),
        ];        $form    = $this->createForm(ToyType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }
        
        return $this->render('frontoffice/toys/listetoys.html.twig', [
            'form' => $form->createView(),
            'toys' => $toyRepository->getToyByFiltersAndPaginator(
                $filters,
                HttpQueryHelper::getOrderBy($request),
                HttpQueryHelper::getLimit($request),
                HttpQueryHelper::getOffset($request),
            )
        ]);    }

    #[Route('/detailtoy/{id}',name:'app_frontoffice_detailtoy')]
    public function detailtoy(Request $request, Toy $toy): Response
    {
        $form = $this->createForm(ToyType::class, $toy);
        $form->handleRequest($request);
        return $this->render('frontoffice/toys/detailtoy.html.twig', [
            'toy'  => $toy,
            'form' => $form,
        ]);
    }
}
