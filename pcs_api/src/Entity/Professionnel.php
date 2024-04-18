<?php

namespace App\Entity;

use App\Repository\ProfessionnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumns;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: ProfessionnelRepository::class)]
#[ApiResource]
class Professionnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $societyName = null;

    #[ORM\Column(length: 14)]
    private ?string $siretNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $societyAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 10)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100)]
    private ?string $country = null;

    #[ORM\OneToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $responsable = null;

    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'prestataire', orphanRemoval: true)]
    private Collection $services;

    /**
     * @var Collection<int, Appartement>
     */
    #[ORM\OneToMany(targetEntity: Appartement::class, mappedBy: 'bailleur', orphanRemoval: true)]
    private Collection $appartements;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->appartements = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->societyName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSocietyName(): ?string
    {
        return $this->societyName;
    }

    public function setSocietyName(string $societyName): static
    {
        $this->societyName = $societyName;

        return $this;
    }

    public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    public function setSiretNumber(string $siretNumber): static
    {
        $this->siretNumber = $siretNumber;

        return $this;
    }

    public function getSocietyAddress(): ?string
    {
        return $this->societyAddress;
    }

    public function setSocietyAddress(string $societyAddress): static
    {
        $this->societyAddress = $societyAddress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getResponsable(): ?User
    {
        return $this->responsable;
    }

    public function setResponsable(?User $responsable): static
    {
        $this->responsable = $responsable;
        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setPrestataire($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getPrestataire() === $this) {
                $service->setPrestataire(null);
            }
        }

        return $this;
    }

    public function isFilled(): bool
    {
        return $this->societyName !== null
        && $this->siretNumber !== null
        && $this->societyAddress !== null
        && $this->city !== null
        && $this->postalCode !== null
        && $this->country !== null;
    }

    /**
     * @return Collection<int, Appartement>
     */
    public function getAppartements(): Collection
    {
        return $this->appartements;
    }

    public function addAppartement(Appartement $appartement): static
    {
        if (!$this->appartements->contains($appartement)) {
            $this->appartements->add($appartement);
            $appartement->setBailleur($this);
        }

        return $this;
    }

    public function removeAppartement(Appartement $appartement): static
    {
        if ($this->appartements->removeElement($appartement)) {
            // set the owning side to null (unless already changed)
            if ($appartement->getBailleur() === $this) {
                $appartement->setBailleur(null);
            }
        }

        return $this;
    }
}
