<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Recette::class)]
    private Collection $recettes;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }

    // Méthode pour obtenir l'ID de la catégorie
    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode pour obtenir le nom de la catégorie
    public function getName(): ?string
    {
        return $this->name;
    }

    // Méthode pour définir le nom de la catégorie
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    // Méthode pour obtenir la collection de recettes liées à cette catégorie
    /**
     * @return Collection<int, Recette>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    // Méthode pour ajouter une recette à la catégorie
    public function addRecette(Recette $recette): static
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->setCategorie($this);
        }

        return $this;
    }

    // Méthode pour supprimer une recette de la catégorie
    public function removeRecette(Recette $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            // Définir le côté propriétaire à null (sauf si cela a déjà été modifié)
            if ($recette->getCategorie() === $this) {
                $recette->setCategorie(null);
            }
        }

        return $this;
    }

    // Méthode magique pour obtenir une représentation en chaîne de la catégorie (utilisée pour l'affichage)
    public function __toString(): string
    {
        return $this->name;
    }
}
