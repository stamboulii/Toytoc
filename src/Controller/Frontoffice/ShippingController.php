<?php

namespace App\Controller\Frontoffice;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Toys_order;
use App\Repository\OrderRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Toy;

#[IsGranted('ROLE_PARENT')]
#[Route('/shipping')]
class ShippingController extends AbstractController
{
    #[Route('/', name: 'app_shipping')]
    public function index(CartService $cartservice): Response
    {
        return $this->render('frontoffice/shipping/index.html.twig', [
            'total' => $cartservice->getTotalPrice(),
        ]);
    }

    #[Route('/add', name: 'shipping_order_add')]
    public function add(OrderRepository $orderRepository, Request $request): Response
    {
        $toys  = array_map(fn(Toy $toy): int => $toy->getId(), $request->getSession()->get('card', []));
        $Toys_order = (new Toys_order())->setBuyer($this->getUser())->setToys($toys);
        $orderRepository->save($Toys_order, true);
        $request->getSession()->remove('card');

        return $this->redirectToRoute('app_front', [], Response::HTTP_SEE_OTHER);
    }
}
