<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec ce pseudo')]
class Utilisateur implements UserInterface,  PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom_utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mot_de_passe_hash = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $jeton = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $jetonExpireAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomUtilisateur(): ?string
    {
        return $this->nom_utilisateur;
    }

    public function setNomUtilisateur(string $nom_utilisateur): static
    {
        $this->nom_utilisateur = $nom_utilisateur;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasseHash(): ?string
    {
        return $this->mot_de_passe_hash;
    }

    public function setMotDePasseHash(string $mot_de_passe_hash): static
    {
        $this->mot_de_passe_hash = $mot_de_passe_hash;

        return $this;
    }

    public function getJeton(): ?string
    {
        return $this->jeton;
    }

    public function setJeton(string $jeton): static
    {
        $this->jeton = $jeton;

        return $this;
    }

    public function getJetonExpireAt(): ?\DateTimeInterface
    {
        return $this->jetonExpireAt;
    }

    public function setJetonExpireAt(?\DateTimeInterface $jetonExpireAt): self
    {
        $this->jetonExpireAt = $jetonExpireAt;
        return $this;
    }

    public function isJetonValide(): bool
{
    return $this->jetonExpireAt !== null && new \DateTime() < $this->jetonExpireAt;
}


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;  // Par exemple, utiliser l'email comme identifiant unique
    }

    public function getRoles(): array
    {
        // Retourner les rôles (si vous n'avez pas de rôles spécifiques, retournez un rôle par défaut)
        return ['ROLE_USER'];
    }

    public function getPassword(): string
    {
        return $this->mot_de_passe_hash; // Utiliser le champ du mot de passe haché
    }

public function eraseCredentials(): void
    {
        // Cette méthode est utilisée pour effacer des informations sensibles temporaires,
        // par exemple, en cas de mot de passe en clair stocké temporairement.
        // Si vous n'avez rien à effacer, vous pouvez laisser cette méthode vide.
    }

}
