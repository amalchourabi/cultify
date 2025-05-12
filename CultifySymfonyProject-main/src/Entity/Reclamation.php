<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_reclamation = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 40,
        minMessage: "Le titre doit comporter au moins {{ limit }} caractères.",
        maxMessage: "La titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        max: 300,
        minMessage: "La description doit comporter au moins {{ limit }} caractères.",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $priorite = null;

    #[ORM\OneToOne(mappedBy: 'reclamation', targetEntity: Reponse::class, cascade: ['persist', 'remove'])]
    private ?Reponse $reponse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $piece_jointe = null;

    #[Assert\Email(
        message: "L'email '{{ value }}' n'est pas valide.",
        mode: "strict"
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;   
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reclamations')]
#[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: true, onDelete: "SET NULL")]
private ?User $idUser = null;

public function getIdUser(): ?User
{
    return $this->idUser;
}

public function setIdUser(?User $idUser): self
{
    $this->idUser = $idUser;

    return $this;
}
public function getId(): ?int
    {
        return $this->id_reclamation;
    }

    public function setId(int $id_reclamation): static
    {
        $this->id_reclamation = $id_reclamation;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): static
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getReponse(): ?Reponse
    {
        return $this->reponse;
    }

    public function setReponse(?Reponse $reponse): static
    {
        if ($reponse === null && $this->reponse !== null) {
            $this->reponse->setReclamation(null);
        }

        if ($reponse !== null && $reponse->getReclamation() !== $this) {
            $reponse->setReclamation($this);
        }

        $this->reponse = $reponse;
        return $this;
    }

    public function getPieceJointe(): ?string
    {
        return $this->piece_jointe;
    }

    public function setPieceJointe(?string $piece_jointe): static
    {
        $this->piece_jointe = $piece_jointe;
        return $this;
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

    public function getEmail(): ?string
{
    return $this->email;
}

public function setEmail(?string $email): static
{
    $this->email = $email;
    return $this;
}
}
