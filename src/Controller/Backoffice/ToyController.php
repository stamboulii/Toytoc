<?php

namespace App\Controller\Backoffice;
use App\Entity\Toy;
use App\Form\Backoffice\Toy\ToyFilterType;
use App\Form\Backoffice\Toy\ToyType;
use App\Helper\HttpQueryHelper;
use App\Repository\ToyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;

#[Route('/toy', name: 'app_backoffice_toy_')]
class ToyController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(ToyRepository $toyRepository, Request $request): Response
    {
        $filters = [
            'user_id' => $request->query->get('user_id'),
            'category_id' => $request->query->get('category_id'),
        ];
        $form = $this->createForm(ToyFilterType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }
        return $this->render('backoffice/toys/toys.html.twig', [
            'form' => $form->createView(),
            'toys' => $toyRepository->getToyByFiltersAndPaginator(
                $filters,
                HttpQueryHelper::getOrderBy($request),
                HttpQueryHelper::getLimit($request),
                HttpQueryHelper::getOffset($request),
            )
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ToyRepository $toyRepository ): Response
    {
        $toy = new Toy();
        $form = $this->createForm(ToyType::class, $toy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $toyRepository->save($toy, true);

            return $this->redirectToRoute('app_backoffice_toy_index', [], Response::HTTP_SEE_OTHER);
        }
//        if ($form->isSubmitted() && $form->isValid()) {
//            /** @var UploadedFile $pictureFile */
//            $pictureFile = $form->get('toy')->getData();
//            if ($pictureFile) {
//                $pictureFileName = $fileUploader->upload($pictureFile);
//                $toy->setPicture($pictureFileName);
//            }
//        }

            return $this->render('backoffice/toys/new.html.twig', [
            'toy' => $toy,
            'form' => $form,

        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Toy $toy, ToyRepository $toyRepository,FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ToyType::class, $toy);
        $form->handleRequest($request);
//        $toy->setPicture(
//            new File($this->getParameter('toy_directory').'/'.$toy->getPicture())
//        );
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('toy')->getData();
            if ($pictureFile) {
                $pictureFileName = $fileUploader->upload($pictureFile);
                $toy->setPicture($pictureFileName);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $toyRepository->save($toy, true);

            return $this->redirectToRoute('app_backoffice_toy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/toys/edit.html.twig', [
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
}
