<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
#[Vich\Uploadable]
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

    #[ORM\Column(length: 255)]
    private ?string $temps_de_preparation = null;

    #[ORM\Column(length: 255)]
    private ?string $temps_de_cuisson = null;

    #[ORM\Column(length: 255)]
    private ?string $difficulte = null;

    #[ORM\Column(length: 255)]
    private ?string $ingredient = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'recettes', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    // Méthode pour obtenir l'ID de la recette
    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode pour obtenir le titre de la recette
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    // Méthode pour définir le titre de la recette
    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    // Méthode pour obtenir la description de la recette
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Méthode pour définir la description de la recette
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    // Méthode pour obtenir le temps de préparation de la recette
    public function getTempsDePreparation(): ?string
    {
        return $this->temps_de_preparation;
    }

    // Méthode pour définir le temps de préparation de la recette
    public function setTempsDePreparation(string $temps_de_preparation): static
    {
        $this->temps_de_preparation = $temps_de_preparation;

        return $this;
    }

    // Méthode pour obtenir le temps de cuisson de la recette
    public function getTempsDeCuisson(): ?string
    {
        return $this->temps_de_cuisson;
    }

    // Méthode pour définir le temps de cuisson de la recette
    public function setTempsDeCuisson(string $temps_de_cuisson): static
    {
        $this->temps_de_cuisson = $temps_de_cuisson;

        return $this;
    }

    // Méthode pour obtenir la difficulté de la recette
    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    // Méthode pour définir la difficulté de la recette
    public function setDifficulte(string $difficulte): static
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    // Méthode pour obtenir les ingrédients de la recette
    public function getIngredient(): ?string
    {
        return $this->ingredient;
    }

    // Méthode pour définir les ingrédients de la recette
    public function setIngredient(string $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    // Méthode pour obtenir l'utilisateur qui a créé la recette
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Méthode pour définir l'utilisateur qui a créé la recette
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Méthode pour obtenir la catégorie de la recette
    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    // Méthode pour définir la catégorie de la recette
    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    // Méthode pour obtenir la collection de commentaires liés à la recette
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    // Méthode pour ajouter un commentaire à la recette
    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRecette($this);
        }

        return $this;
    }

    // Méthode pour supprimer un commentaire de la recette
    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // Définir le côté propriétaire à null (sauf si déjà modifié)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }

    // Méthode pour définir le fichier image de la recette
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // Il est nécessaire qu'au moins un champ change si vous utilisez Doctrine,
            // sinon les écouteurs d'événements ne seront pas appelés et le fichier sera perdu
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    // Méthode pour obtenir le fichier image de la recette
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    // Méthode pour définir le nom du fichier image de la recette
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    // Méthode pour obtenir le nom du fichier image de la recette
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    // Méthode pour définir la taille du fichier image de la recette
    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    // Méthode pour obtenir la taille du fichier image de la recette
    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    // Méthode pour obtenir la date de mise à jour de la recette
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    // Méthode pour définir la date de mise à jour de la recette
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    // Méthode pour obtenir le titre de la recette sous forme de chaîne de caractères
    public function __toString(): string
    {
        return $this->titre;
    }
}
