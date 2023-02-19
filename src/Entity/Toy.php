<?php

namespace App\Entity;

use App\Repository\ToyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;




#[ORM\Entity(repositoryClass: ToyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Toy
{
    use Trait\CreatedUpdatedAtTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $weight = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'toys')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'toys')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File()]
    private ?string $picture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
