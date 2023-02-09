<?php

namespace App\Controller\Backoffice;



use App\Entity\User;
use App\Form\Backoffice\User\UserFilterType;
use App\Repository\UserRepository;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/index', name: 'app_backoffice_index')]
    public function index(UserRepository $userRepository): Response
    {


        return $this->render('backoffice/home/index.html.twig', [
            'controller_name' => 'HomeController',
            'curr_nav' => 'user',

        ]);
    }


}
