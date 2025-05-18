<?php

namespace App\Entity;

use App\Repository\OrdreProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdreProduitRepository::class)]
class OrdreProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ordre::class, inversedBy: 'ordreProduits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ordre $ordre = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;

    public function __construct(?Produit $produit = null, int $quantity = 1)
    {
        $this->produit = $produit;
        $this->quantity = $quantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdre(): ?Ordre
    {
        return $this->ordre;
    }

    public function setOrdre(?Ordre $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException("La quantité doit être au moins égale à 1.");
        }
        $this->quantity = $quantity;

        return $this;
    }
}
