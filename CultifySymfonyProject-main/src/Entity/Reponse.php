<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_reponse")]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de réponse est obligatoire.")]
    private ?\DateTimeInterface $dateReponse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 40,
        minMessage: "Le titre doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        max: 300,
        minMessage: "Le contenu doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le contenu ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $contenu = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'offre ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 40,
        minMessage: "L'offre doit comporter au moins {{ limit }} caractères.",
        maxMessage: "L'offre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $offre = null;

    #[ORM\OneToOne(targetEntity: Reclamation::class, inversedBy: 'reponse')]
    #[ORM\JoinColumn(name: 'id_reclamation', referencedColumnName: 'id_reclamation', nullable: false)]
    #[Assert\NotNull(message: "Une réclamation associée est requise.")]
    private ?Reclamation $reclamation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $piece_jointe = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reponses')]
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
        return $this->id;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->dateReponse;
    }

    public function setDateReponse(?\DateTimeInterface $dateReponse): static
    {
        $this->dateReponse = $dateReponse;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getOffre(): ?string
    {
        return $this->offre;
    }

    public function setOffre(?string $offre): static
    {
        $this->offre = $offre;
        return $this;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): static
    {
        $this->reclamation = $reclamation;
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
}