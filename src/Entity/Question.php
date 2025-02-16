<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reponses_correcte = null;

    #[ORM\Column(length: 255)]
    private ?string $text_question = null;

    #[ORM\Column(length: 255)]
    private ?string $reponsePropose1 = null;

    #[ORM\Column(length: 255)]
    private ?string $reponsePropose2 = null;

    #[ORM\Column(length: 255)]
    private ?string $reponsePropose3 = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    private ?QuizInteractif $idQuiz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponsesCorrecte(): ?string
    {
        return $this->reponses_correcte;
    }

    public function setReponsesCorrecte(string $reponses_correcte): static
    {
        $this->reponses_correcte = $reponses_correcte;

        return $this;
    }

    public function getTextQuestion(): ?string
    {
        return $this->text_question;
    }

    public function setTextQuestion(string $text_question): static
    {
        $this->text_question = $text_question;

        return $this;
    }

    public function getReponsePropose1(): ?string
    {
        return $this->reponsePropose1;
    }

    public function setReponsePropose1(string $reponsePropose1): static
    {
        $this->reponsePropose1 = $reponsePropose1;

        return $this;
    }

    public function getReponsePropose2(): ?string
    {
        return $this->reponsePropose2;
    }

    public function setReponsePropose2(string $reponsePropose2): static
    {
        $this->reponsePropose2 = $reponsePropose2;

        return $this;
    }

    public function getReponsePropose3(): ?string
    {
        return $this->reponsePropose3;
    }

    public function setReponsePropose3(string $reponsePropose3): static
    {
        $this->reponsePropose3 = $reponsePropose3;

        return $this;
    }

    public function getIdQuiz(): ?QuizInteractif
    {
        return $this->idQuiz;
    }

    public function setIdQuiz(?QuizInteractif $idQuiz): static
    {
        $this->idQuiz = $idQuiz;

        return $this;
    }
}
