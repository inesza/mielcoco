<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $unite;

    /**
     * @ORM\Column(type="integer")
     */
    private $prix_unitaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Composition", inversedBy="produit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $composition;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(string $unite): self
    {
        $this->unite = $unite;

        return $this;
    }

    public function getPrixUnitaire(): ?int
    {
        return $this->prix_unitaire;
    }

    public function setPrixUnitaire(int $prix_unitaire): self
    {
        $this->prix_unitaire = $prix_unitaire;

        return $this;
    }

    public function getComposition(): ?Composition
    {
        return $this->composition;
    }

    public function setComposition(?Composition $composition): self
    {
        $this->composition = $composition;

        return $this;
    }
}
