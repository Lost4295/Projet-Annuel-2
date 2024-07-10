<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use DateInterval;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    public const PRESTA_NETTOYAGE = 1;
    public const PRESTA_ELEC = 2;
    public const PRESTA_PLOMBERIE = 3;
    public const PRESTA_PEINTURE = 4;
    public const PRESTA_BRICOLAGE = 5;
    public const PRESTA_CHAUFFEUR = 6;

    public const PRESTA_LIST = [
        self::PRESTA_NETTOYAGE => 'Nettoyage',
        self::PRESTA_ELEC => 'Electricité',
        self::PRESTA_PLOMBERIE => 'Plomberie',
        self::PRESTA_PEINTURE => 'Peinture',
        self::PRESTA_BRICOLAGE => 'Bricolage',
        self::PRESTA_CHAUFFEUR => 'Chauffeur',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 10)]
    private ?string $numero = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $typePresta = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $contactWithPhone = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'devis')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Professionnel::class, inversedBy: 'devis')]
    private ?Professionnel $prestataire = null;

    #[ORM\Column(length:50, nullable: true)]
    private ?string $estimatedTime = null;

    #[ORM\Column]
    private ?bool $isOk = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;
    
    #[ORM\Column(nullable: true)]
    private ?bool $toValidate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sid = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    #[ORM\Column(nullable: true)]
    private ?bool $turn = false; // FALSE = PRESTA, TRUE = CLIENT


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public static function getTypePrestaString()
    {
        return self::PRESTA_LIST;
    }
    public function getTypePresta(): ?int
    {
        return $this->typePresta;
    }

    public function setTypePresta(int $typePresta): static
    {
        $this->typePresta = $typePresta;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription() :?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description) :static
    {
        $this->description = $description;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPrestataire(): ?Professionnel
    {
        return $this->prestataire;
    }

    public function setPrestataire(?Professionnel $prestataire): static
    {
        $this->prestataire = $prestataire;

        return $this;
    }

    public function getContactWithPhone(): ?bool
    {
        return $this->contactWithPhone;
    }

    public function setContactWithPhone(bool $contactWithPhone): static
    {
        $this->contactWithPhone = $contactWithPhone;

        return $this;
    }

    public function getEstimatedTime(): ?string
    {
        return $this->estimatedTime;
    }

    public function setEstimatedTime(string $estimatedTime): static
    {
        $this->estimatedTime = $estimatedTime;

        return $this;
    }

    public function getOk(): ?bool
    {
        return $this->isOk;
    }

    public function setOk(bool $isOk): static
    {
        $this->isOk = $isOk;

        return $this;
    }

    public function getToValidate(): ?bool
    {
        return $this->toValidate;
    }

    public function setToValidate(bool $toValidate): static
    {
        $this->toValidate = $toValidate;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getSid(): ?string
    {
        return $this->sid;
    }

    public function setSid(?string $sid): static
    {
        $this->sid = $sid;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function isTurn(): ?bool
    {
        return $this->turn;
    }

    public function setTurn(?bool $turn): static
    {
        $this->turn = $turn;

        return $this;
    }


}
