<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\Backoffice\User\SearchUsers;
use App\Form\Backoffice\User\UserFilterType;
use App\Form\Backoffice\User\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/users')]
class UserCrudController extends AbstractController
{
    #[Route('/', name: 'app_backoffice_users' , methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $cr=$this->getUser();
        $searchUsers = new SearchUsers();
        $form = $this->createForm(UserFilterType::class,$searchUsers
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //dd($data->q);
            $searchUsers->page = $request->query->getInt('page',1);
            $users = $userRepository->findBySearch($searchUsers);
            dd($users);

            return $this->render('backoffice/users/users.html.twig', [
                'form' => $form,
                'users' => $users
            ]);
        }

        return $this->render('backoffice/users/users.html.twig', [
            'form' => $form->createView(),
            'users' => $userRepository->findAllExcept($cr)
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(userType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_backoffice_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/users/new.html.twig', [
            'user' => $user,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('backoffice/users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_backoffice_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backoffice/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_backoffice_users', [], Response::HTTP_SEE_OTHER);
    }

}
