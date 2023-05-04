<?php

namespace App\Controller\Frontoffice;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use App\Entity\Toy;
use App\Repository\OrderRepository;
use App\Repository\ShippingRepository;
use App\Repository\ToyRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ManagerRegistry;


class ShippingController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}
    #[Route('/shipping', name: 'app_shipping')]
    public function index(CartService $cartservice): Response
    {
        $user =  $this->getUser();
        return $this->render('frontoffice/shipping/index.html.twig', [
            'controller_name' => 'ShippingController',
            'total'=> $cartservice->getTotalPrice(),
            'user' => $user,
        ]);
    }

    #[Route('/add',name:'shipping_order_add')]
    public function add (OrderRepository $orderRepository, Toy $toy ,Request $request,ToyRepository $toyRepository): Response
    {
        $order = new Order();
        $cards = $request->getSession()->get('card', []);
        foreach ($cards as $card){
           
         //$verif= $toyRepository ->getRepository(Toy::class)->find($card[$toy->getId()]);
        //  $entityManager = $this->doctrine->getManager();
        // $toyRepository = $entityManager->getRepository(Toy::class);
        // $id=$card.$toy->getId();
        // $verif=$toyRepository->findBy(['id'=> $id]);
        // $verif = isset($card[$toy->getId()]) ? $card[$toy->getId()] : null;
        $toyId = $toy->getId();
        if (is_array($card) && array_key_exists($toyId, $card)) {
            $verif = $card[$toyId];
        } else {
            $verif = null;
        }

           if(null!==$verif){ $order ->addToy($card[$toy->getId()]);}
           
        }
        
        $order ->setBuyer($this->getUser());
        $orderRepository->save($order,true);
        $card->clear();
        return $this->redirectToRoute('app_front', [], Response::HTTP_SEE_OTHER);


    }
}
