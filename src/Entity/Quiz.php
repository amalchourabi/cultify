<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_quiz", type: "integer")]
    private ?int $idQuiz = null;

    #[ORM\Column(length: 255)]
    private ?string $TitreQuiz = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $DateQuiz = null;

    #[ORM\Column]
    private ?int $ScoreQuiz = null;

    #[ORM\Column(length: 255)]
    private ?string $ReponseChoisit = null;

    /**
     * @var Collection<int, Question>
     */

    private Collection $Questions;

    #[ORM\ManyToOne(targetEntity: ContenuMultiMedia::class, inversedBy: "quizs")]
    #[ORM\JoinColumn(name: "contenu_id", referencedColumnName: "id_contenu", nullable: true)]

    private ?ContenuMultiMedia $contenuMultiMedia = null;

   


    public function __construct()
    {
        $this->Questions = new ArrayCollection();
    }

    public function getIdQuiz(): ?int
    {
        return $this->idQuiz;
    }

    public function getTitreQuiz(): ?string
    {
        return $this->TitreQuiz;
    }

    public function setTitreQuiz(string $TitreQuiz): static
    {
        $this->TitreQuiz = $TitreQuiz;

        return $this;
    }

    public function getDateQuiz(): ?\DateTimeInterface
{
    return $this->DateQuiz;
}

public function setDateQuiz(?\DateTimeInterface $DateQuiz): self
{
    $this->DateQuiz = $DateQuiz;
    return $this;
}

    public function getScoreQuiz(): ?int
    {
        return $this->ScoreQuiz;
    }

    public function setScoreQuiz(int $ScoreQuiz): static
    {
        $this->ScoreQuiz = $ScoreQuiz;

        return $this;
    }

    public function getReponseChoisit(): ?string
    {
        return $this->ReponseChoisit;
    }

    public function setReponseChoisit(string $ReponseChoisit): static
    {
        $this->ReponseChoisit = $ReponseChoisit;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->Questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->Questions->contains($question)) {
            $this->Questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->Questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

    public function getContenuMultiMedia(): ?ContenuMultiMedia
    {
        return $this->contenuMultiMedia;
    }

    public function setContenuMultiMedia(?ContenuMultiMedia $contenuMultiMedia): static
    {
        $this->contenuMultiMedia = $contenuMultiMedia;

        return $this;
    }

    
}
