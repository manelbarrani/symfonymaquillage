<?php


// src/Entity/Ordre.php

namespace App\Entity;

use App\Repository\OrdreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\OrdreProduit;

#[ORM\Entity(repositoryClass: OrdreRepository::class)]
class Ordre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'float')]
    private float $total;

    #[ORM\OneToMany(mappedBy: 'ordre', targetEntity: OrdreProduit::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ordreProduits;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->ordreProduits = new ArrayCollection();
        $this->createdAt = new \DateTime(); // Initialiser la date de création
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|OrdreProduit[]
     */
    public function getOrdreProduits(): Collection
    {
        return $this->ordreProduits;
    }

    public function addOrdreProduit(OrdreProduit $ordreProduit): self
    {
        if (!$this->ordreProduits->contains($ordreProduit)) {
            $this->ordreProduits[] = $ordreProduit;
            $ordreProduit->setOrdre($this);
        }

        return $this;
    }

    public function removeOrdreProduit(OrdreProduit $ordreProduit): self
    {
        if ($this->ordreProduits->removeElement($ordreProduit)) {
            // Set the owning side to null (unless already changed)
            if ($ordreProduit->getOrdre() === $this) {
                $ordreProduit->setOrdre(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
        private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
