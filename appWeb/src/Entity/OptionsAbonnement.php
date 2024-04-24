<?php

namespace App\Entity;

use App\Repository\OptionsAbonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionsAbonnementRepository::class)]
class OptionsAbonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?bool $presence = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info = null;

    #[ORM\ManyToMany(targetEntity: Abonnement::class, inversedBy: 'options')]
    private Collection $abonnement;

    public function __construct()
    {
        $this->abonnement = new ArrayCollection();
    }

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

    public function isPresence(): ?bool
    {
        return $this->presence;
    }

    public function setPresence(bool $presence): static
    {
        $this->presence = $presence;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(string $info): static
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnement(): Collection
    {
        return $this->abonnement;
    }

    public function addAbonnement(Abonnement $abonnement): static
    {
        if (!$this->abonnement->contains($abonnement)) {
            $this->abonnement->add($abonnement);
        }

        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): static
    {
        $this->abonnement->removeElement($abonnement);

        return $this;
    }
}
