<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: "id_r", type: "integer")]
    private ?int $id_r = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "L'état de la réservation est obligatoire.")]
    #[Assert\Choice(choices: ["annulé", "confirmé"], message: "L'état doit être soit 'annulé' soit 'confirmé'.")]
    private ?string $etat = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: "La date de réservation ne peut pas être vide.")]
    private ?\DateTimeInterface $date_r = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le thème est obligatoire.")]
    private ?string $theme = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url(message: "L'URL doit être valide.")]
    private ?string $url = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message: "Le nombre de tickets est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de tickets doit être un entier positif.")]
    private ?int $nb_tickets = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: "reservations")]
    #[ORM\JoinColumn(name: "id_e", referencedColumnName: "id_e", nullable: false)]
    #[Assert\NotNull(message: "Un événement doit être associé à la réservation.")]
    private ?Event $event = null;

    public function __construct()
    {
        // Définir la date de réservation par défaut à aujourd'hui
        $this->date_r = new \DateTime();
    }

    // Getters et Setters

    public function getIdR(): ?int
    {
        return $this->id_r;
    }

    public function setIdR(int $id_r): static
    {
        $this->id_r = $id_r;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    public function getDateR(): ?\DateTimeInterface
    {
        return $this->date_r;
    }

    public function setDateR(\DateTimeInterface $date_r): static
    {
        $this->date_r = $date_r;
        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getNbTickets(): ?int
    {
        return $this->nb_tickets;
    }

    public function setNbTickets(int $nb_tickets): static
    {
        $this->nb_tickets = $nb_tickets;
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;
        return $this;
    }
}
