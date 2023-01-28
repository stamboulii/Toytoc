<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

///admin/reset-password
#[Route('/reset-password', name: 'app_backoffice_reset_password_')]
class ResetPasswordController extends AbstractController
{
    #[Route('/request', name: 'request')]
    public function request(): Response
    {
        return $this->render('backoffice/reset_password/request.html.twig');
    }
}
