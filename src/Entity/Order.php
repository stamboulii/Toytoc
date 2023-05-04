<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;


#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'toys_order')]
#[ORM\HasLifecycleCallbacks]
class Order
{
    use Traits\CreatedUpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column(length: 100)]
    private ?string $reference = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'orders')]
    private ?User $buyer = null;

    #[ORM\Column(type: 'json')]
    private array $toys = [];

    #[ORM\OneToOne(inversedBy: 'order', targetEntity: Shipping::class, cascade: ['persist', 'remove'])]
    private Shipping $shipping;

    public function __construct()
    {
        $this->reference = uniqid();
        $this->orderDate = new \DateTime();
        $this->shipping = (new Shipping())->setOrder($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): self
    {
        $this->buyer = $buyer;
        $this->shipping->setAddress(sprintf('%s, %s-%s', $buyer->getAddress(), $buyer->getZipCode(), $buyer->getCity()));

        return $this;
    }

    public function getToys(): array
    {
        return $this->toys;
    }

    public function setToys(array $toys): self
    {
        $this->toys = $toys;

        return $this;
    }

    public function getShipping(): ?Shipping
    {
        return $this->shipping;
    }

    public function setShipping(?Shipping $shipping): self
    {
        $this->shipping = $shipping;

        return $this;
    }
}
