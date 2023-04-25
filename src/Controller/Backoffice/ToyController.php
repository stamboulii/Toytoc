<?php

namespace App\Controller\Backoffice;

use App\Entity\Toy;
use App\Form\Backoffice\Toy\ToyFilterType;
use App\Form\Backoffice\Toy\ToyType;
use App\Helper\HttpQueryHelper;
use App\Repository\ToyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/secured/toy', name: 'app_backoffice_toy_')]
class ToyController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(ToyRepository $toyRepository, Request $request): Response
    {
        $filters = [
            'user_id'     => $request->query->get('user_id'),
            'category_id' => $request->query->get('category_id'),
        ];
            $form    = $this->createForm(ToyFilterType::class)->handleRequest($request);
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
    public function new(Request $request, ToyRepository $toyRepository, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ToyType::class, $toy = new Toy());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            foreach ($form->get('images')->getData() as $image) {
                $toy->addPicture((new Picture())->setPath($fileUploader->upload($image)));
            }

            $toyRepository->save($toy, true);

            return $this->redirectToRoute('app_backoffice_toy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/toys/new.html.twig', [
            'toy'  => $toy,
            'form' => $form,

        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Toy $toy, ToyRepository $toyRepository, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ToyType::class, $toy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->createQueryBuilder()
                ->from(Picture::class, 'picture')
                ->delete()
                ->where('picture.toy = :toy')
                ->setParameter('toy', $toy)
                ->getQuery()
                ->execute();

            /** @var UploadedFile $image */
            foreach ($form->get('images')->getData() as $image) {
                $toy->addPicture((new Picture())->setPath($fileUploader->upload($image)));
            }

            $toyRepository->save($toy, true);

            return $this->redirectToRoute('app_backoffice_toy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/toys/edit.html.twig', [
            'toy'  => $toy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Toy $toy, ToyRepository $toyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $toy->getId(), $request->request->get('_token'))) {
            $toyRepository->remove($toy, true);
        }

        return $this->redirectToRoute('app_backoffice_toy_index', [], Response::HTTP_SEE_OTHER);
    }
}
