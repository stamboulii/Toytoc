<?php

namespace App\Controller\Backoffice;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route('/index', name: 'app_backoffice_index')]
    public function index(OrderRepository $orderRepository): Response
    {
        // $filters = ['order'=> $orderRepository->findAll()];

        return $this->render('backoffice/home/index.html.twig', [
            'controller_name' => 'HomeController',
            'curr_nav' => 'user',
            // 'order' => $filters,

        ]);
    }
}
