<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\Backoffice\User\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function index(Request $request,UserRepository $userRepository,FileUploader $fileUploader): Response
  {


        $form = $this->createForm(RegistrationFormType::class, $user = User::newAdmin());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form['picture']->getData();
            $user->setPicture($fileUploader->upload($image));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_backoffice_index', [], Response::HTTP_SEE_OTHER);
        }

      return $this->render('backoffice/register/index.html.twig', [
            'user' => $user,
            'form' => $form,

        ]);
    }
}
