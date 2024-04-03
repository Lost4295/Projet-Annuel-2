<?php

namespace App\Entity;

use App\Repository\ProfessionnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProfessionnelRepository::class)]
class Professionnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $societyName = null;

    #[ORM\Column(length: 255)]
    private ?string $siretNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $societyAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 10)]
    private ?string $postalCode = null;

    #[Assert\Country()]
    #[ORM\Column(length: 100)]
    private ?string $country = null;

    #[ORM\OneToOne(mappedBy: 'professionnel', cascade: ['persist', 'remove'])]
    private ?User $responsable = null;

    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'prestataire', orphanRemoval: true)]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
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
        // unset the owning side of the relation if necessary
        if ($responsable === null && $this->responsable !== null) {
            $this->responsable->setProfessionnel(null);
        }

        // set the owning side of the relation if necessary
        if ($responsable !== null && $responsable->getProfessionnel() !== $this) {
            $responsable->setProfessionnel($this);
        }

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
}
