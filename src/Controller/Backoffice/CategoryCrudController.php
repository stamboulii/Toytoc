<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Form\Backoffice\Category\CategoryFilterType;
use App\Form\Backoffice\Category\CategoryType;
use App\Helper\HttpQueryHelper;
use App\Repository\CategoryRepository;;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories',name:'app_backoffice_categories_')]
class CategoryCrudController extends AbstractController
{
    #[Route('/category', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository,Request $request): Response
    {

        $filters = ['category'=> $categoryRepository->findAll()];
        $form = $this->createForm(CategoryFilterType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }
        return $this->render('backoffice/categories/index.html.twig', [
            'form'  => $form->createView(),
            'categories' => $categoryRepository->getCategoryByFiltersAndPaginator(
                $filters,
                HttpQueryHelper::getOrderBy($request),
                HttpQueryHelper::getLimit($request),
                HttpQueryHelper::getOffset($request),
            )
        ]);
    }
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_backoffice_categories_category', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/categories/new.html.twig', [
            'category' => $category,
            'form' => $form,

        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_backoffice_categories_category', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_backoffice_categories_delete', [], Response::HTTP_SEE_OTHER);
    }
}
