<?php

namespace App\Controller\Backoffice;

use App\Entity\ContactUs;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Toys_order;
use App\Repository\ContactUsRepository;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{
    #[Route('/secured/index', name: 'app_backoffice_index')]
    public function index(OrderRepository $orderRepository,ContactUsRepository $contactUsRepository,ManagerRegistry $doctrine,UserRepository $userRepository): Response
    {
        // $filters = ['order'=> $orderRepository->findAll()];
        $contact = $contactUsRepository->findAll();
        // $users =  ['user' => $this->getUser()->getId(), 'roles' => User::ROLE_PARENT];
        $userCount = count($userRepository->findAll());
        $orderCount = count($orderRepository->findAll());
        return $this->render('backoffice/home/index.html.twig', [
            'controller_name' => 'HomeController',
            'curr_nav' => 'user',
            'orderCount' => $orderCount,
            'messages' => $contact,
            'userCount' => $userCount,

        ]);
    }

    #[Route('/{id}/detail', name: 'contactUs_detail', methods: ['GET', 'POST'])]
    public function edit($id,ContactUsRepository $contactUsRepository,UserRepository $userRepository): Response
    {
        $orderCount = count($orderRepository->findAll());
        $contact = $contactUsRepository->findAll();
        $message = $contactUsRepository->find($id);
        $userCount = count($userRepository->findAll());
        return $this->render('frontoffice/contact/contactusdetail.html.twig', [
            'messages' => $contact,
            'message' => $message,
            'userCount' => $userCount,
            'orderCount' => $orderCount,
           
        ]);
    }

    #[Route('/{id}', name: 'contactUs_delete', methods: ['POST'])]
    public function delete(Request $request, ContactUs $contactUs, ContactUsRepository $contactUsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contactUs->getId(), $request->request->get('_token'))) {
            $contactUsRepository->remove($contactUs, true);
        }

        return $this->redirectToRoute('contactUs_delete', [], Response::HTTP_SEE_OTHER);
    }
}
