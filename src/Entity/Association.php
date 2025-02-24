<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
class Association
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true )]
    #[Assert\NotBlank(message: "Le nom de l'association ne peut pas être vide.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom de l'association doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'association ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\NotBlank(message: "Le montant du don ne peut pas être vide.")]
    #[Assert\Positive(message: "Le montant du don doit être un nombre positif.")]
    #[Assert\Type(type: 'numeric', message: "Le montant doit être un nombre.")]
    private ?float $montantDesire = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le contact ne peut pas être vide.")]
    #[Assert\Email(message: "L'adresse email '{{ value }}' n'est pas valide.")]
    private ?string $contact = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le but de l'association ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        minMessage: "Le but de l'association doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $but = null;

    /**
     * @var Collection<int, Don>
     */
    #[ORM\OneToMany(targetEntity: Don::class, mappedBy: 'association', cascade: ['remove'])]
    private Collection $IdDon;

    public function __construct()
    {
        $this->IdDon = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }
    public function getMontantDesire(): ?float
    {
        return $this->montantDesire;
    }

    public function setMontantDesire(?float $montantDesire): static
    {
        $this->montantDesire = $montantDesire;
        return $this;
    }

    public function getMontantActuel(): float
    {
        $montantTotal = 0;
        foreach ($this->IdDon as $don) {
            if ($don->getStatus() === 'confirme') {
                $montantTotal += $don->getMontant();
            }
        }
        return $montantTotal;
    }

    public function getPourcentageProgression(): float
    {
        if ($this->montantDesire <= 0) {
            return 0;
        }
        return min(100, ($this->getMontantActuel() / $this->montantDesire) * 100);
    }

    public function getBut(): ?string
    {
        return $this->but;
    }

    public function setBut(?string $but): static
    {
        $this->but = $but;

        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }
    
    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection<int, Don>
     */
    public function getIdDon(): Collection
    {
        return $this->IdDon;
    }

    public function addIdDon(Don $idDon): static
    {
        if (!$this->IdDon->contains($idDon)) {
            $this->IdDon->add($idDon);
            $idDon->setAssociation($this);
        }

        return $this;
    }

    public function removeIdDon(Don $idDon): static
    {
        if ($this->IdDon->removeElement($idDon)) {
            // set the owning side to null (unless already changed)
            if ($idDon->getAssociation() === $this) {
                $idDon->setAssociation(null);
            }
        }

        return $this;
    }
}