<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorieRepository")
 */
class Categorie
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Recette", inversedBy="categories")
     */
    private $recettes;

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

    /**
     * @return Collection|Recette[]
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes[] = $recette;
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->recettes->contains($recette)) {
            $this->recettes->removeElement($recette);
        }

        return $this;
    }
}
