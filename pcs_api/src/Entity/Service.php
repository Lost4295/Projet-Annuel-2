<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ApiResource( security: "is_granted('ROLE_NON_USER')")]
class Service
{
    const NETTOYAGE = 1;
    const ELECTRICITE = 2;
    const PLOMBERIE = 3;
    const PEINTURE = 4;
    const BRICOLAGE = 5;
    const CHAUFFEUR = 6;
    const TYPE_LIST =
        [
            "nettoyage" => self::NETTOYAGE,
            "electricité" => self::ELECTRICITE,
            "plomberie" => self::PLOMBERIE,
            "peinture" => self::PEINTURE,
            "bricolage" => self::BRICOLAGE,
            "chauffeur" => self::CHAUFFEUR
        ];


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Professionnel $prestataire;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $tarifs = null;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\ManyToMany(targetEntity: Location::class, mappedBy: 'services')]
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->titre;
    }

    public function getId(): ?int
    {
        return $this->id;
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


    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPrestataire(): Professionnel
    {
        return $this->prestataire;
    }

    public function setPrestataire(?Professionnel $prestataire): static
    {
        $this->prestataire = $prestataire;

        return $this;
    }

    public function getTarifs(): ?string
    {
        return $this->tarifs;
    }

    public function setTarifs(string $tarifs): static
    {
        $this->tarifs = $tarifs;

        return $this;
    }
    public static function getTypes(): array
    {
        return self::TYPE_LIST;
    }
    
    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): static
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->addService($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): static
    {
        if ($this->locations->removeElement($location)) {
            $location->removeService($this);
        }

        return $this;
    }
}
