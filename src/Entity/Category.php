<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Category
{
    use Trait\CreatedUpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(min: 5, max: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(min: 10, max: 100)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Toy::class)]
    private Collection $toys;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $picture = null;

    public function __construct()
    {
        $this->toys = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
            $toy->setCategory($this);
        }

        return $this;
    }

    public function removeToy(Toy $toy): self
    {
        if ($this->toys->removeElement($toy)) {
            // set the owning side to null (unless already changed)
            if ($toy->getCategory() === $this) {
                $toy->setCategory(null);
            }
        }

        return $this;
    }

    public function setToys(Collection $toys): self
    {
        $this->toys = $toys;

        return $this;
    }
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
