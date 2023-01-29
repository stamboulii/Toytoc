<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use App\Form\Backoffice\User\ResetPasswordRequestType;

#[Route('/reset-password', name: 'app_backoffice_reset_password_')]
class ResetPasswordController extends AbstractController
{
    #[Route('/request', name: 'request', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function request(Request $request, UserRepository $repository): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $repository->findOneBy(['email' => $email = $form->get('email')->getData()]);
            if (!$user) {
                $this->addFlash('warning', sprintf('User with email %s not found', $email));

                return $this->redirectToRoute('app_backoffice_reset_password_request');
            }

            $user->setResetPasswordToken((new UriSafeTokenGenerator(1024))->generateToken());
            $user->setResetPasswordRequestAt(new \DateTime());

            $repository->save($user, true);

            // sending mail
            $this->addFlash('success', 'Request resetting password successfully, please check your email');

            return $this->redirectToRoute('app_backoffice_login');
        }

        return $this->render('backoffice/reset_password/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
