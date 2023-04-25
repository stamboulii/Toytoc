<?php

namespace App\Service;

use App\Entity\Toy;
use Symfony\Component\HttpFoundation\RequestStack;


class CartService 
{
    protected RequestStack $requestStack;
    public function __construct(RequestStack $requestStack){
        $this->requestStack = $requestStack;
    }

    public function addCart(Toy $toy): bool
    {
        $card = $this->requestStack->getSession()->get('card', []);
        if (!array_key_exists($toy->getId(), $card)) {
            $card[$toy->getId()] = $toy;
        }

        $this->requestStack->getSession()->set('card', $card);

        return true;
    }

    public function remove(Toy $toy): bool{
        $card = $this->requestStack->getSession()->get('card', []);
        if (!array_key_exists($toy->getId(), $card)) {
            return false;
        }

        unset($card[$toy->getId()]);
        $this->requestStack->getSession()->set('card', $card);

        return true;
    }

    public function getTotalPrice():float {
        return array_sum(array_map(fn (Toy $toy) : float => $toy->getPrice(), $this->requestStack->getSession()->get('card', [])));
    }
}