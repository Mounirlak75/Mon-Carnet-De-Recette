<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'commentairesPostes')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Recette $recette = null;

    // Méthode pour obtenir l'ID du commentaire
    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode pour obtenir le contenu du commentaire
    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    // Méthode pour définir le contenu du commentaire
    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    // Méthode pour obtenir la date du commentaire
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    // Méthode pour définir la date du commentaire
    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    // Méthode pour obtenir l'utilisateur qui a laissé le commentaire
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Méthode pour définir l'utilisateur qui a laissé le commentaire
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Méthode pour obtenir la recette associée à ce commentaire
    public function getRecette(): ?Recette
    {
        return $this->recette;
    }
    
    // Méthode pour définir la recette associée à ce commentaire
    public function setRecette(?Recette $recette): static
    {
        $this->recette = $recette;
    
        return $this;
    }
}
