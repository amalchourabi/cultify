<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: "id_e", type: "integer")]
    private ?int $id_e = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $titre = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;


    #[ORM\Column(type: "date")]
    #[Assert\NotBlank(message: "La date ne peut pas être vide.")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être valide.")]
    private ?\DateTimeInterface $date_e = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "L'organisation ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'organisation ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $organisation = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "La capacité ne peut pas être vide.")]
    #[Assert\Positive(message: "La capacité doit être un nombre positif.")]
    private ?int $capacite = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le nombre de places ne peut pas être vide.")]
    #[Assert\Positive(message: "Le nombre de places doit être un nombre positif.")]
    #[Assert\LessThanOrEqual(
        propertyPath: "capacite",
        message: "Le nombre de places ne peut pas être supérieur à la capacité."
    )]
    private ?int $nbplaces = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "La catégorie ne peut pas être vide.")]
    #[Assert\Choice(
        choices: ["Théâtre", "Musique", "Sport", "Musée", "Gastronomie"],
        message: "Choisissez une catégorie valide."
    )]
    private ?string $categorie = null;
    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]   
      private ?float $prix = null;
      #[ORM\Column(type: "string", length: 255)]
 
     #[Assert\Length(
        max: 255,
        maxMessage: "L'organisation ne peut pas dépasser {{ limit }} caractères."
    )]
      private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Reservation::class, cascade: ['persist', 'remove'])]
    private Collection $reservations;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?float $latitude = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getIdE(): ?int
    {
        return $this->id_e;
    }

    public function setIdE(int $id_e): static
    {
        $this->id_e = $id_e;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    

    public function getDateE(): ?\DateTimeInterface
    {
        return $this->date_e;
    }

    public function setDateE(\DateTimeInterface $date_e): static
    {
        $this->date_e = $date_e;
        return $this;
    }

    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    public function setOrganisation(string $organisation): static
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function getNbplaces(): ?int
    {
        return $this->nbplaces;
    }

    public function setNbplaces(int $nbplaces): static
    {
        $this->nbplaces = $nbplaces;
        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }
    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setEvent($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getEvent() === $this) {
                $reservation->setEvent(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->id_e;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }
#[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'events')]
#[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: true, onDelete: "SET NULL")]
private ?User $idUser = null;

public function getIdUser(): ?User
{
    return $this->idUser;
}

public function setIdUser(?User $user): self
{
    $this->idUser = $user;
    return $this;
}



}
