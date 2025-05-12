<?php

namespace App\Entity;

use App\Repository\ContenuMultiMediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContenuMultiMediaRepository::class)]

class ContenuMultiMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_contenu", type: "integer")]
    private ?int $idContenu = null;

    #[ORM\Column(length: 255)]
    private ?string $TitreMedia = null;

    #[ORM\Column(length: 255)]
    private ?string $TextMedia = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)] 
    
    private ?string $PhotoMedia = null;

    #[ORM\Column(length: 255)]
    private ?string $CategorieMedia = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
private ?\DateTime $DateMedia = null;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'contenuMultiMedia')]
    private Collection $Quizs;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'contenusMultiMedia')]
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
    

    public function __construct()
    {
        $this->Quizs = new ArrayCollection();
    }

    public function getIdContenu(): ?int
    {
        return $this->idContenu;
    }

    public function getTitreMedia(): ?string
    {
        return $this->TitreMedia;
    }

    public function setTitreMedia(string $TitreMedia): static
    {
        $this->TitreMedia = $TitreMedia;

        return $this;
    }

    public function getTextMedia(): ?string
    {
        return $this->TextMedia;
    }

    public function setTextMedia(string $TextMedia): static
    {
        $this->TextMedia = $TextMedia;

        return $this;
    }

    public function getPhotoMedia(): ?string
    {
        return $this->PhotoMedia;
    }

    public function setPhotoMedia(string $PhotoMedia): static
    {
        $this->PhotoMedia = $PhotoMedia;

        return $this;
    }

   public function getCategorieMedia(): ?string
    {
        return $this->CategorieMedia;
   }

    public function setCategorieMedia(string $CategorieMedia): static
    {
       $this->CategorieMedia = $CategorieMedia;

       return $this;
    }
    public function getDateMedia(): ?\DateTimeInterface
    {
        return $this->DateMedia;
    }

    public function setDateMedia(?\DateTimeInterface $DateMedia): self
    {
        $this->DateMedia = $DateMedia;
        return $this;
    }



    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizs(): Collection
    {
        return $this->Quizs;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->Quizs->contains($quiz)) {
            $this->Quizs->add($quiz);
            $quiz->setContenuMultiMedia($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->Quizs->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getContenuMultiMedia() === $this) {
                $quiz->setContenuMultiMedia(null);
            }
        }

        return $this;
    }

    public function getCategorieMediaFormatted(): string
    {
        return str_replace('|', ', ', $this->CategorieMedia);
    }
}
