<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[UniqueEntity(fields: ['location', 'user'], message: 'Vous avez déjà noté cette location.')]
#[UniqueEntity(fields: ['service', 'user'], message: 'Vous avez déjà noté ce service.')]
#[UniqueEntity(fields: ['prestataire', 'user'], message: 'Vous avez déjà noté ce prestataire.')]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Location $location = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: true)]

    private ?Professionnel $prestataire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of service
     */ 
    public function getService() : ?Service
    {
        return $this->service;
    }

    /**
     * Set the value of service
     *
     * @return  self
     */ 
    public function setService(?Service $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get the value of prestataire
     */ 
    public function getPrestataire()
    {
        return $this->prestataire;
    }

    /**
     * Set the value of prestataire
     *
     * @return  self
     */ 
    public function setPrestataire($prestataire)
    {
        $this->prestataire = $prestataire;

        return $this;
    }
}
