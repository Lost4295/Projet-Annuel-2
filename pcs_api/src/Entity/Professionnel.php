<?php

namespace App\Entity;

use App\Repository\ProfessionnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: ProfessionnelRepository::class)]
#[ApiResource( security: "is_granted('ROLE_NON_USER')")]
class Professionnel
{
    const DAYS_LIST = [
        "lundi",
        "mardi",
        "mercredi",
        "jeudi",
        "vendredi",
        "samedi",
        "dimanche"
    ];

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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length:4)]
    private ?string $avgNote = "0";

    #[ORM\Column(type: 'json', nullable: true)]
    private array $workDays = [];

    #[ORM\Column(length:20, nullable: true)]
    private ?string $startHour = null;
    #[ORM\Column(length:20, nullable: true)]
    private ?string $endHour = null;
    
    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'prestataire', orphanRemoval: true)]
    private Collection $devis;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $prestatype;

    #[ORM\Column()]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $justification = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->appartements = new ArrayCollection();
        $this->image = "user-placeholder.jpg";
        $this->workDays = [];
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

    public function setServices(Collection $services): static
    {
        $this->services = $services;

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

    public function setAppartements(Collection $appartements): static
    {
        $this->appartements = $appartements;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        if ($image !== null) {
            $this->image = $image;
        } else {
            $this->image = "user-placeholder.jpg";
        }

        return $this;
    }
    
    /**
     * Get the value of startHour
     */ 
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     * Set the value of startHour
     *
     * @return  self
     */ 
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;

        return $this;
    }

    /**
     * Get the value of endHour
     */ 
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     * Set the value of endHour
     *
     * @return  self
     */ 
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;

        return $this;
    }

    public function getWorkDays(): array
    {
        return$this->workDays;
    }

    /**
     * @param list<string> $workDay
     */
    public function setWorkDays(array $workDays): static
    {
        foreach ($workDays as $workDay) {
            if (!in_array($workDay, array_keys(self::DAYS_LIST))) {
                throw new \InvalidArgumentException("Invalid work day : $workDay");
            }
        }
        $this->workDays = $workDays;
        return $this;
    }

    public function addWorkDay(int $workDay): static
    {
        if (!in_array($workDay, $this->workDays) && in_array($workDay,array_keys(self::DAYS_LIST))) {
            $this->workDays[] = $workDay;
        }
        $this->setWorkDays(array_unique($this->workDays));
        return $this;
    }

    public function removeWorkDay(int $workDay): static
    {
        $key = array_search($workDay, $this->workDays);
        if ($key !== false) {
            unset($this->workDays[$key]);
        }
        return $this;
    }

    /**
     * Get the value of avgNote
     */ 
    public function getAvgNote()
    {
        return $this->avgNote;
    }

    /**
     * Set the value of avgNote
     *
     * @return  self
     */ 
    public function setAvgNote($avgNote)
    {
        $this->avgNote = $avgNote;
        return $this;
    }
    
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevis(Devis $devis): static
    {
        if (!$this->devis->contains($devis)) {
            $this->devis->add($devis);
            $devis->setPrestataire($this);
        }

        return $this;
    }

    public function removeDevis(Devis $devis): static
    {
        if ($this->devis->removeElement($devis)) {
            // set the owning side to null (unless already changed)
            if ($devis->getPrestataire() === $this) {
                $devis->setPrestataire(null);
            }
        }

        return $this;
    }

    public function getPrestaType(): ?int
    {
        return $this->prestatype;
    }

    public function setPrestaType(int $prestatype): static
    {
        $this->prestatype = $prestatype;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getJustification(): ?string
    {
        return $this->justification;
    }

    public function setJustification(?string $justification): static
    {
        $this->justification = $justification;

        return $this;
    }
}
