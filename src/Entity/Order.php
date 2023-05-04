<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: OrderRepository::class)]
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

    

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $buyer = null;

    #[ORM\OneToMany(mappedBy: 'orderr', targetEntity: Toy::class,cascade: ['persist', 'remove'])]
    private Collection $toys;

    public function __construct()
    {
        $this->toys = new ArrayCollection();
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

        return $this;
    }

    /**
     * @return Collection<int, Toy>
     */
    public function getToys(): Collection
    {
        return $this->toys;
    }

    public function addToy(Toy $toy): self
    {
        if (!$this->toys->contains($toy)) {
            $this->toys->add($toy);
            $toy->setOrderr($this);
        }

        return $this;
    }

    public function removeToy(Toy $toy): self
    {
        if ($this->toys->removeElement($toy)) {
            // set the owning side to null (unless already changed)
            if ($toy->getOrderr() === $this) {
                $toy->setOrderr(null);
            }
        }

        return $this;
    }
}
