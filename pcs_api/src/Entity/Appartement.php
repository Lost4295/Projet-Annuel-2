<?php

namespace App\Entity;

use App\Repository\AppartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: AppartementRepository::class)]
#[ApiResource]
// #[ApiResource( security: "is_granted('ROLE_NON_USER')")]
class Appartement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $shortDesc = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;
    
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nbVoyageurs = null;

    #[ORM\Column]
    private ?float $note = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $images;

    #[ORM\ManyToOne(inversedBy: 'appartements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Professionnel $bailleur = null;


    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nbchambers = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nbbathrooms = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $nbBeds = null;


    /**
     * @var Collection<int, AppartPlus>
     */
    #[ORM\ManyToMany(targetEntity: AppartPlus::class, inversedBy: 'appartement')]
    #[ORM\JoinTable(name: 'scp_appartement_appart_plus')]
    private Collection $appartPluses;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column]
    private ?float $surface = null;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\OneToMany(targetEntity: Location::class, mappedBy: 'appartement')]
    private Collection $locations;


    public function __construct()
    {
        $this->state = "En attente";
        $this->note = 0;
        $this->images = ["house-placeholder.jpg"];
        $this->appartPluses = new ArrayCollection();
        $this->locations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->shortDesc;
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

    public function getShortDesc(): ?string
    {
        return $this->shortDesc;
    }

    public function setShortDesc(string $shortDesc): static
    {
        $this->shortDesc = $shortDesc;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param list<string> $image
     */
    public function setImages(array $image): static
    {
        $this->images = $image;
        return $this;
    }

    public function addImage(string $image): static
    {
        $this->images[] = $image;
        $this->setImages(array_unique($this->images));
        return $this;
    }

    public function removeImage(string $image): static
    {
        $key = array_search($image, $this->images);
        if ($key !== false) {
            unset($this->images[$key]);
        }
        return $this;
    }
    public function getBailleur(): ?Professionnel
    {
        return $this->bailleur;
    }

    public function setBailleur(?Professionnel $bailleur): static
    {
        $this->bailleur = $bailleur;

        return $this;
    }
    public function getCity(): ?string
    {
        return $this->city;
    }
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }
    public function getCountry(): ?string
    {
        return $this->country;
    }
    public function  setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }
    public function  setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }
    public function  setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
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

    public function getNbchambers(): ?int
    {
        return $this->nbchambers;
    }

    public function setNbchambers(int $nbchambers): static
    {
        $this->nbchambers = $nbchambers;

        return $this;
    }

    public function getNbbathrooms(): ?int
    {
        return $this->nbbathrooms;
    }

    public function setNbbathrooms(int $nbbathrooms): static
    {
        $this->nbbathrooms = $nbbathrooms;

        return $this;
    }

    public function getNbBeds(): ?int
    {
        return $this->nbBeds;
    }

    public function setNbBeds(int $nbBeds): static
    {
        $this->nbBeds = $nbBeds;

        return $this;
    }

    /**
     * @return Collection<int, AppartPlus>
     */
    public function getAppartPluses(): Collection
    {
        return $this->appartPluses;
    }
    public function getNbVoyageurs(): ?int
    {
        return $this->nbVoyageurs;
    }

    public function setNbVoyageurs(int $nbVoyageurs): static
    {
        $this->nbVoyageurs = $nbVoyageurs;

        return $this;
    }
    public function addAppartPlus(AppartPlus $appartPlus): static
    {
        if (!$this->appartPluses->contains($appartPlus)) {
            $this->appartPluses->add($appartPlus);
            $appartPlus->addAppartement($this);
        }

        return $this;
    }

    public function removeAppartPlus(AppartPlus $appartPlus): static
    {
        $this->appartPluses->removeElement($appartPlus);

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(float $surface): static
    {
        $this->surface = $surface;

        return $this;
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
            $this->locations->add($location);
            $location->setAppartement($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): static
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getAppartement() === $this) {
                $location->setAppartement(null);
            }
        }

        return $this;
    }
}
