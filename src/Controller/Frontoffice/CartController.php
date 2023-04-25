<?php

namespace App\Controller\Frontoffice;

use App\Entity\Toy;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    #[Route("/cart", name:"app_frontoffice_cart")]
    public function index(CartService $cartservice): Response
    {
        return $this -> render('frontoffice/cart/cart.html.twig', [
            'total'=> $cartservice->getTotalPrice()
        ]); 
    }

    #[Route("/cart/add/{id}", name:"app_frontoffice_cart_add", methods: [Request::METHOD_POST])]
    public function add(Toy $toy, CartService $cartservice)
    {
        if ($cartservice->addCart($toy)) {
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'message' => 'Toy cannot be added to card.']);
    }
   
   
    #[Route("/remove/{id}", name:"app_frontoffice_cartremove", methods: [Request::METHOD_POST])]
    public function removefromcart(Toy $toy, CartService $cartservice)
    {
        if ($cartservice->remove($toy)) {
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false, 'message' => 'Toy cannot be removed from card.']);
    }
} 
