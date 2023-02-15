<?php

namespace App\Controller\Backoffice;

use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Backoffice\User\UserFilterType;
use App\Helper\HttpQueryHelper;
use App\Entity\User;
use App\Form\Backoffice\User\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user', name: 'app_backoffice_user_')]
class UserController extends AbstractController
{
    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $filters = ['user' => $this->getUser()->getId(), 'roles' => User::ROLE_PARENT];
        $form    = $this->createForm(UserFilterType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        return $this->render('backoffice/users/index.html.twig', [
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
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user = User::newParent());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_backoffice_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/users/new.html.twig', [
            'user' => $user,
            'form' => $form,

        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_backoffice_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/users/edit.html.twig', [
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

        return $this->redirectToRoute('app_backoffice_user_delete', [], Response::HTTP_SEE_OTHER);
    }
}
