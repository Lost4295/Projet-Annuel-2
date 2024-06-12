<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Ignore;


#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ApiResource( security: "is_granted('ROLE_NON_USER')")]
class Option
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info = null;


    #[ORM\OneToMany(targetEntity: OptionsAbonnement::class, mappedBy: 'option')]
    #[Ignore]
    private Collection $abonnement;



    public function __construct()
    {
        $this->abonnement = new ArrayCollection();
    }
    public function getId(): ?string
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
     * @return Collection<int, OptionsAbonnement>
     */
    public function getAbonnement(): Collection
    {
        return $this->abonnement;
    }

    public function addAbonnement(OptionsAbonnement $abonnement): static
    {
        if (!$this->abonnement->contains($abonnement)) {
            $this->abonnement->add($abonnement);
        }
        return $this;
    }

    public function removeAbonnement(OptionsAbonnement $abonnement): static
    {
        $this->abonnement->removeElement($abonnement);

        return $this;
    }
}
