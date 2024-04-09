<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Professionnel $prestataire;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $tarifs = null;

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


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
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
}
