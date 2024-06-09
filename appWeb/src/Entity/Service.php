<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
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
            self::NETTOYAGE=> "nettoyage" ,
            self::ELECTRICITE=> "electricité" ,
            self::PLOMBERIE=> "plomberie" ,
            self::PEINTURE=> "peinture" ,
            self::BRICOLAGE=> "bricolage" ,
            self::CHAUFFEUR=> "chauffeur"
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


    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'service', orphanRemoval: true)]
    private ?Collection $notes = null;

    #[ORM\Column(length:4)]
    private string $avgNote = "0";
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $additionalInfo = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $images;
    public function __construct()
    {
        $this->locations = new ArrayCollection();
        $this->images = ["default.png"];
        $this->notes = new ArrayCollection();
        $this->avgNote = 0;
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

    /**
     * Get the value of note
     */ 
    public function getNotes()
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setService($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            $note->setService(null);
        }

        return $this;
    }

    public function getImages(): array
    {
        return$this->images; 
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
    public function getAdditionalInfo(): ?string
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(?string $additionalInfo): static
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }
}
