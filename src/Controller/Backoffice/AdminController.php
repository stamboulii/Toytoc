<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\Backoffice\User\UserFilterType;
use App\Form\Backoffice\User\UserType;
use App\Helper\HttpQueryHelper;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/secured/admin', name: 'app_backoffice_admin_')]
class AdminController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $filters = ['user' => $this->getUser()->getId(), 'roles' => User::ROLE_ADMIN];
        $form = $this->createForm(UserFilterType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        return $this->render('backoffice/Admins/index.html.twig', [
            'form'  => $form->createView(),
            'users' => $userRepository->getUsersByFiltersAndPaginator(
                $filters,
                HttpQueryHelper::getOrderBy($request),
                HttpQueryHelper::getLimit($request),
                HttpQueryHelper::getOffset($request),
            )
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository,FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UserType::class, $user = User::newAdmin());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form['picture']->getData();
            $user->setPicture($fileUploader->upload($image));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_backoffice_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/Admins/new.html.twig', [
            'user' => $user,
            'form' => $form,

        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository,FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form['picture']->getData();
            $user->setPicture($fileUploader->upload($image));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_backoffice_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/Admins/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_backoffice_admin_delete', [], Response::HTTP_SEE_OTHER);
    }
}
