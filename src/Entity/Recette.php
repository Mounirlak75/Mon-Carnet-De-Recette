<?php
namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $temps_de_preparation = null;

    #[ORM\Column]
    private ?int $temps_de_cuisson = null;

    #[ORM\Column]
    private ?int $difficulte = null;

    #[ORM\Column(length: 255)]
    private ?string $ingredient = null;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'Recettes', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTempsDePreparation(): ?int
    {
        return $this->temps_de_preparation;
    }

    public function setTempsDePreparation(int $temps_de_preparation): static
    {
        $this->temps_de_preparation = $temps_de_preparation;

        return $this;
    }

    public function getTempsDeCuisson(): ?int
    {
        return $this->temps_de_cuisson;
    }

    public function setTempsDeCuisson(int $temps_de_cuisson): static
    {
        $this->temps_de_cuisson = $temps_de_cuisson;

        return $this;
    }

    public function getDifficulte(): ?int
    {
        return $this->difficulte;
    }

    public function setDifficulte(int $difficulte): static
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    public function getIngredient(): ?string
    {
        return $this->ingredient;
    }

    public function setIngredient(string $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // /**
    //  * @return Collection<int, Categorie>
    //  */
    // public function getCategorie(): Collection
    // {
    //     return $this->categorie;
    // }

    // public function addCategorie(Categorie $categorie): static
    // {
    //     if (!$this->categorie->contains($categorie)) {
    //         $this->categorie->add($categorie);
    //         $categorie->setRecette($this);
    //     }

    //     return $this;
    // }

    // public function removeCategorie(Categorie $categorie): static
    // {
    //     if ($this->categorie->removeElement($categorie)) {
    //         // set the owning side to null (unless already changed)
    //         if ($categorie->getRecette() === $this) {
    //             $categorie->setRecette(null);
    //         }
    //     }

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Etape>
    //  */
    // public function getEtape(): Collection
    // {
    //     return $this->etape;
    // }

    // public function addEtape(Etape $etape): static
    // {
    //     if (!$this->etape->contains($etape)) {
    //         $this->etape->add($etape);
    //     }

    //     return $this;
    // }

    // public function removeEtape(Etape $etape): static
    // {
    //     $this->etape->removeElement($etape);

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Ingredient>
    //  */
    // public function getIngredients(): Collection
    // {
    //     return $this->ingredients;
    // }

    // public function addIngredient(Ingredient $ingredient): static
    // {
    //     if (!$this->ingredients->contains($ingredient)) {
    //         $this->ingredients->add($ingredient);
    //     }

    //     return $this;
    // }

    // public function removeIngredient(Ingredient $ingredient): static
    // {
    //     $this->ingredients->removeElement($ingredient);

    //     return $this;
    // }

    public function __toString(): string
    {
        return $this->titre;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRecette($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }
}