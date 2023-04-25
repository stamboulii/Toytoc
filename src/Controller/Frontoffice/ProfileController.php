<?php

namespace App\Controller\Frontoffice;


use App\Entity\Picture;
use App\Repository\ToyRepository;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Backoffice\User\UserFilterType;
use App\Entity\User;
use App\Form\Backoffice\User\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Toy;


class ProfileController extends AbstractController 
{

    #[Route('/profile', name: 'app_frontoffice_profile', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $filters = ['user' => $this->getUser()->getId()];
        $form    = $this->createForm(UserFilterType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = array_merge($filters, $form->getData());
        }

        return $this->render('frontoffice/profile/profile.html.twig', [
            'form'  => $form->createView(),
            'user' => $userRepository->$filters,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_frontoffice_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository,FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form['picture']->getData();
            $user->setPicture($fileUploader->upload($image));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_frontoffice_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/profile/profile.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_frontoffice_profile_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_backoffice_user_delete', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_frontoffice_profile_newtoy', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('app_frontoffice_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/profile/profile.html.twig', [
            'toy'  => $toy,
            'form' => $form,

        ]);
    }
    
    #[Route('/{id}', name: 'app_frontoffice_profile__deletetoy', methods: ['POST'])]
    public function deletetoy(Request $request, Toy $toy, ToyRepository $toyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $toy->getId(), $request->request->get('_token'))) {
            $toyRepository->remove($toy, true);
        }

        return $this->redirectToRoute('app_frontoffice_profile', [], Response::HTTP_SEE_OTHER);
    }
    
}
