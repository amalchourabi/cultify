<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.", maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom ne peut pas être vide.")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.", maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom d'utilisateur ne peut pas être vide.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom d'utilisateur doit contenir au moins {{ limit }} caractères.", maxMessage: "Le nom d'utilisateur ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 8, maxMessage: "Le numéro de téléphone ne peut pas dépasser {{ limit }} chiffres.")]
    #[Assert\Regex(pattern: "/^[0-9]+$/", message: "Le numéro de téléphone doit contenir uniquement des chiffres.")]
    private ?string $num_tel = null;
    //
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'adresse e-mail ne peut pas être vide.")]
    #[Assert\Email(message: "L'adresse e-mail '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(choices: ["male", "female"], message: "Veuillez choisir un genre valide.")]
    private ?string $gender = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\LessThan(value: "today -10 years", message: "L'utilisateur doit avoir au moins 10 ans.")]
    private ?\DateTimeInterface $Datedenaissance = null;


    #[ORM\Column(type: 'string', length: 255, nullable: true)] 
    private ?string $profilepicture = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide.")]
    #[Assert\Length(min: 6, max: 255, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.", maxMessage: "Le mot de passe ne peut pas dépasser {{ limit }} caractères.")]
    private string $password;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: "float", message: "Le montant doit être un nombre.")]
    private ?float $MontantApayer = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private $ocrData = [];

    #[ORM\OneToMany(targetEntity: Don::class, mappedBy: 'idUser')]
    private Collection $dons;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'idUser')]
    private Collection $reservations;

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'idUser')]
    private Collection $reponses;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'idUser')]
    private Collection $reclamations;

    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'idUser')]
    private Collection $quizzes;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'idUser')]
    private Collection $questions;

    #[ORM\OneToMany(targetEntity: ContenuMultiMedia::class, mappedBy: 'idUser')]
    private Collection $contenusMultiMedia;

    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'idUser')]
    private Collection $events;

    public function __construct()
    {
        $this->dons = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reponses = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->contenusMultiMedia = new ArrayCollection();
        $this->events = new ArrayCollection();
    }
    public function getUserIdentifier(): string
    {
        return $this->email; // or return $this->username if you use a username field
    }
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getRoles(): array { return array_unique(array_merge($this->roles, ['ROLE_USER'])); }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }
    public function getDatedenaissance(): ?\DateTimeInterface { return $this->Datedenaissance; }
    public function setDatedenaissance(?\DateTimeInterface $Datedenaissance): self { $this->Datedenaissance = $Datedenaissance; return $this; }
    public function eraseCredentials(): void {}
    public function getProfilepicture(): ?string
    {
        return $this->profilepicture;
    }
    
    public function setProfilepicture(string $profilepicture): static
    {
        $this->profilepicture= $profilepicture;  
        return $this; 
    }
public function getNumTel(): ?string
    {
        return $this->num_tel;
    }

    public function setNumTel(?string $num_tel): static
    {
        $this->num_tel = $num_tel;

        return $this;
    }
    public function getUsername(): ?string
    {
        return $this->username;  
    }
    
    public function setUsername(string $username): static
    {
        $this->username = $username;  
        return $this; 
    }



public function addOcrData(array $newOcrData): self
{
    $this->ocrData[] = $newOcrData;
    return $this;
}

    public function getOcrData(): ?array
    {
        return $this->ocrData ?? [];
    }
    
    public function setOcrData(array $ocrData): self
    {
        $this->ocrData = $ocrData;
        return $this;
    }
    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }
    public function getMontantApayer(): ?float
    {
        return $this->MontantApayer;
    }

    public function setMontantApayer(?float $MontantApayer): static
    {
        $this->MontantApayer = $MontantApayer;

        return $this;
    }


    // Add similar getter, adder, and remover methods for all relations (dons, reservations, etc.)
}
