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
use App\Entity\User;
use App\Form\Backoffice\User\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Toy;
use App\Form\Backoffice\Toy\ToyType;

#[Route('/secured/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_frontoffice_profile', methods: ['GET'])]
    public function index(): Response
    {
        $user =  $this->getUser();

        return $this->render('frontoffice/profile/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit', name: 'app_frontoffice_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRepository $userRepository, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UserType::class, $user =  $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form['picture']->getData();
            $user->setPicture($fileUploader->upload($image));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_frontoffice_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/profile/editprofile.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_frontoffice_profile_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_backoffice_user_delete', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_frontoffice_profile_addtoy', methods: ['GET', 'POST'])]
    public function new(Request $request, ToyRepository $toyRepository, FileUploader $fileUploader): Response
    {
        $user =  $this->getUser();
        $form = $this->createForm(ToyType::class, $toy = (new Toy())->setUser($user), ['connected_user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            foreach ($form->get('images')->getData() as $image) {
                $toy->addPicture((new Picture())->setPath($fileUploader->upload($image)));
            }

            $toyRepository->save($toy, true);

            return $this->redirectToRoute('app_frontoffice_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontoffice/profile/addtoy.html.twig', [
            'toy'  => $toy,
            'form' => $form,
        ]);
    }

    #[Route('/toys', name: 'app_frontoffice_profile_toys', methods: ['GET'])]
    public function toys( ToyRepository $toyRepository): Response
    {
        return $this->render('frontoffice/profile/yourtoys.html.twig', [
            'toys' => $toyRepository->getToysByUser($this->getUser()),
        ]);
    }

    #[Route('/delete-toy/{id}', name: 'app_frontoffice_profile__deletetoy', methods: ['POST'])]
    public function deletetoy(Request $request, Toy $toy, ToyRepository $toyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $toy->getId(), $request->request->get('_token'))) {
            $toyRepository->remove($toy, true);
        }

        return $this->redirectToRoute('app_frontoffice_profile', [], Response::HTTP_SEE_OTHER);
    }

}
