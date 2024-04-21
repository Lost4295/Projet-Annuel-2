<?php

namespace App\Entity;

use App\Repository\AppartPlusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppartPlusRepository::class)]
class AppartPlus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, appartement>
     */
    #[ORM\ManyToMany(targetEntity: appartement::class, inversedBy: 'appartPluses')]
    private Collection $appartement;

    public function __construct()
    {
        $this->appartement = new ArrayCollection();
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

    /**
     * @return Collection<int, appartement>
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
}
