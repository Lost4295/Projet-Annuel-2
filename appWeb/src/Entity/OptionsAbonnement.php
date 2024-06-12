<?php

namespace App\Entity;

use App\Repository\OptionsAbonnementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionsAbonnementRepository::class)]
class OptionsAbonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $presence = null;

    #[ORM\ManyToOne(targetEntity: Abonnement::class, inversedBy: 'options')]
    private Abonnement $abonnement;
    
    #[ORM\ManyToOne(targetEntity: Option::class, inversedBy: 'abonnement')]
    private Option $option;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Abonnement
     */
    public function getAbonnement(): Abonnement
    {
        return $this->abonnement;
    }

    /**
     * @return Option
     */
    public function getOption(): Option
    {
        return $this->option;
    }


    public function setAbonnement(?Abonnement $abonnement): static
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    public function setOption(?Option $option): static
    {
        $this->option = $option;

        return $this;
    }
}
