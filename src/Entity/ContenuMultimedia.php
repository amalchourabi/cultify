<?php

namespace App\Entity;

use App\Repository\ContenuMultimediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContenuMultimediaRepository::class)]
class ContenuMultimedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $typeMedia = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePub = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMiseAjour = null;

    #[ORM\Column(length: 255)]
    private ?string $categorieMedia = null;

    #[ORM\ManyToOne(inversedBy: 'contenuMultimedia')]
    private ?User $idUser = null;

    /**
     * @var Collection<int, QuizInteractif>
     */
    #[ORM\OneToMany(targetEntity: QuizInteractif::class, mappedBy: 'idContenu')]
    private Collection $quizInteractifs;

    public function __construct()
    {
        $this->quizInteractifs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeMedia(): ?string
    {
        return $this->typeMedia;
    }

    public function setTypeMedia(string $typeMedia): static
    {
        $this->typeMedia = $typeMedia;

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

    public function getDatePub(): ?\DateTimeInterface
    {
        return $this->datePub;
    }

    public function setDatePub(\DateTimeInterface $datePub): static
    {
        $this->datePub = $datePub;

        return $this;
    }

    public function getDateMiseAjour(): ?\DateTimeInterface
    {
        return $this->dateMiseAjour;
    }

    public function setDateMiseAjour(\DateTimeInterface $dateMiseAjour): static
    {
        $this->dateMiseAjour = $dateMiseAjour;

        return $this;
    }

    public function getCategorieMedia(): ?string
    {
        return $this->categorieMedia;
    }

    public function setCategorieMedia(string $categorieMedia): static
    {
        $this->categorieMedia = $categorieMedia;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * @return Collection<int, QuizInteractif>
     */
    public function getQuizInteractifs(): Collection
    {
        return $this->quizInteractifs;
    }

    public function addQuizInteractif(QuizInteractif $quizInteractif): static
    {
        if (!$this->quizInteractifs->contains($quizInteractif)) {
            $this->quizInteractifs->add($quizInteractif);
            $quizInteractif->setIdContenu($this);
        }

        return $this;
    }

    public function removeQuizInteractif(QuizInteractif $quizInteractif): static
    {
        if ($this->quizInteractifs->removeElement($quizInteractif)) {
            // set the owning side to null (unless already changed)
            if ($quizInteractif->getIdContenu() === $this) {
                $quizInteractif->setIdContenu(null);
            }
        }

        return $this;
    }
}
