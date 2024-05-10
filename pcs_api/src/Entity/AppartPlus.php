<?php

namespace App\Entity;

use App\Repository\AppartPlusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: AppartPlusRepository::class)]
#[ApiResource]
class AppartPlus
{
    public const WIFI = 1;
    public const POOL = 2;
    public const JACUZZI = 3;
    public const TV = 4;
    public const PARKING = 5;
    public const WASHING_MACHINE = 6;

    const LIST_PLUSES = [
        self::WIFI => "wifi",
        self::POOL => "pool",
        self::JACUZZI => "jacuzzi",
        self::TV => "tv",
        self::PARKING => "parking",
        self::WASHING_MACHINE => "washing-machine",
    ];

    const ICONS = [
        self::LIST_PLUSES[AppartPlus::WIFI] => ["data-icon" => 'fas fa-wifi'],
        self::LIST_PLUSES[AppartPlus::POOL] => ["data-icon" => 'fas fa-person-swimming'],
        self::LIST_PLUSES[AppartPlus::JACUZZI] => ["data-icon" => 'fas fa-hot-tub-person'],
        self::LIST_PLUSES[AppartPlus::TV] => ["data-icon" => 'fas fa-tv'],
        self::LIST_PLUSES[AppartPlus::PARKING] => ["data-icon" => 'fas fa-square-parking'],
        self::LIST_PLUSES[AppartPlus::WASHING_MACHINE] => ["data-icon" => 'fas fa-shirt'],
    ];


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $icon = null;

    /**
     * @var Collection<int, Appartement>
     */
    #[ORM\ManyToMany(targetEntity: Appartement::class, mappedBy: "appartPluses")]
    private Collection $appartement;

    public function __construct()
    {
        $this->appartement = new ArrayCollection();
    }

    public function __toString(): string
    {
        return self::LIST_PLUSES[$this->icon];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection<int, Appartement>
     */
    public function getAppartement(): Collection
    {
        return $this->appartement;
    }

    public function addAppartement(Appartement $appartement): static
    {
        if (!$this->appartement->contains($appartement)) {
            $this->appartement->add($appartement);
            $appartement->addAppartPlus($this);
        }

        return $this;
    }

    public function removeAppartement(Appartement $appartement): static
    {
        if ($this->appartement->removeElement($appartement)) {
            $appartement->removeAppartPlus($this);
        }

        return $this;
    }

    public static function getAllPluses(): array
    {
        return self::LIST_PLUSES;
    }

    public static function getIcons(): array
    {
        return self::ICONS;
    }
}
