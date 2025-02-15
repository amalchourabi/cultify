<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_question", type: "integer")]

    private ?int $idQuestion = null;

    #[ORM\Column(length: 255)]
    private ?string $TextQuestion = null;

    #[ORM\Column(length: 255)]
    private ?string $ResponseProp1 = null;

    #[ORM\Column(length: 255)]
    private ?string $ResponseProp2 = null;

    #[ORM\Column(length: 255)]
    private ?string $ResponseProp3 = null;

    #[ORM\Column(length: 255)]
    private ?string $ResponseCorrect = null;

    #[ORM\ManyToOne(inversedBy: 'Questions')]
    #[ORM\JoinColumn(name: "quiz_id", referencedColumnName: "id_quiz", nullable: false)]
    private ?Quiz $quiz = null;

    public function getIdQuestion(): ?int
    {
        return $this->idQuestion;
    }

    public function getTextQuestion(): ?string
    {
        return $this->TextQuestion;
    }

    public function setTextQuestion(string $TextQuestion): static
    {
        $this->TextQuestion = $TextQuestion;

        return $this;
    }

    public function getResponseProp1(): ?string
    {
        return $this->ResponseProp1;
    }

    public function setResponseProp1(string $ResponseProp1): static
    {
        $this->ResponseProp1 = $ResponseProp1;

        return $this;
    }

    public function getResponseProp2(): ?string
    {
        return $this->ResponseProp2;
    }

    public function setResponseProp2(string $ResponseProp2): static
    {
        $this->ResponseProp2 = $ResponseProp2;

        return $this;
    }

    public function getResponseProp3(): ?string
    {
        return $this->ResponseProp3;
    }

    public function setResponseProp3(string $ResponseProp3): static
    {
        $this->ResponseProp3 = $ResponseProp3;

        return $this;
    }

    public function getResponseCorrect(): ?string
    {
        return $this->ResponseCorrect;
    }

    public function setResponseCorrect(string $ResponseCorrect): static
    {
        $this->ResponseCorrect = $ResponseCorrect;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }
}
