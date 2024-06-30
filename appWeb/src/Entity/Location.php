<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $dateha = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan(propertyPath: 'dateDebut')]
    private ?\DateTime $dateFin = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Appartement $appartement = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $locataire = null;

    #[ORM\Column]
    private ?int $adults = null;

    #[ORM\Column]
    private ?int $kids = null;

    #[ORM\Column]
    private ?int $babies = null;
    
    #[ORM\OneToOne(inversedBy: 'location')]
    private Fichier $facture;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToMany(targetEntity:Service::class, inversedBy: 'locations')]
    private Collection $services;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'location', orphanRemoval: true)]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->id . ' ' . $this->dateDebut->format('d/m/Y') . ' - ' . $this->dateFin->format('d/m/Y') . ' ' . $this->appartement->getTitre() . ' ' . $this->locataire->getFullName();
    }

    public function getDateha(): ?\DateTimeInterface
    {
        return $this->dateha;
    }
    
    public function setDateha(\DateTimeInterface $dateha): static
    {
        $this->dateha = $dateha;

        return $this;
    }

  
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getAppartement(): ?Appartement
    {
        return $this->appartement;
    }

    public function setAppartement(?Appartement $appartement): static
    {
        $this->appartement = $appartement;

        return $this;
    }

    public function getLocataire(): ?User
    {
        return $this->locataire;
    }

    public function setLocataire(?User $locataire): static
    {
        $this->locataire = $locataire;

        return $this;
    }

    public function getAdults(): ?int
    {
        return $this->adults;
    }

    public function setAdults(int $adults): static
    {
        $this->adults = $adults;

        return $this;
    }

    public function getKids(): ?int
    {
        return $this->kids;
    }

    public function setKids(int $kids): static
    {
        $this->kids = $kids;

        return $this;
    }

    public function getBabies(): ?int
    {
        return $this->babies;
    }

    public function setBabies(int $babies): static
    {
        $this->babies = $babies;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addLocation($this);
        }

        return $this;
    }
    public function getFacture(): ?Fichier
    {
        return $this->facture;
    }

    public function setFacture(?Fichier $facture): static
    {
        $this->facture = $facture;

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            $service->removeLocation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setLocation($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getLocation() === $this) {
                $note->setLocation(null);
            }
        }

        return $this;
    }
}
