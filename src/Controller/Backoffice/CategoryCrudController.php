<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Form\Backoffice\Category\CategoryType;
use App\Repository\CategoryRepository;;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/categories')]
class CategoryCrudController extends AbstractController
{
    #[Route('/', name: 'app_backoffice_categories' )]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('backoffice/categories/categories.html.twig', [
            'category'=> $categoryRepository->findAll()
        ]);
    }
    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_backoffice_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/categories/new.html.twig', [
            'category' => $category,
            'form' => $form,

        ]);
    }

//    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
//    public function show(Category $category): Response
//    {
//
//        return $this->render('backoffice/categories/show.html.twig', [
//            'category' => $category,
//        ]);
//    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('backoffice/categories/categories.html.twig', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_backoffice_categories', [], Response::HTTP_SEE_OTHER);
    }


}
