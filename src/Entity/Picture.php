<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\ManyToOne(targetEntity: Toy::class, inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: false)]
    private Toy $toy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getToy(): Toy
    {
        return $this->toy;
    }

    public function setToy(Toy $toy): self
    {
        $this->toy = $toy;

        return $this;
    }
}
