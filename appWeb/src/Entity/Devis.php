<?php

namespace App\Entity;

use App\Repository\DevisRepository;
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

    public const PRESTA_LIST = [
        self::PRESTA_NETTOYAGE => 'Nettoyage',
        self::PRESTA_ELEC => 'Electricité',
        self::PRESTA_PLOMBERIE => 'Plomberie',
        self::PRESTA_PEINTURE => 'Peinture',
        self::PRESTA_BRICOLAGE => 'Bricolage',
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

    private ?string $description = null;
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
}
