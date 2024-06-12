<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
#[ApiResource( security: "is_granted('ROLE_NON_USER')")]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $tarif = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'abonnement')]
    private Collection $subscribers;

    #[ORM\OneToMany(targetEntity: OptionsAbonnement::class, mappedBy: 'abonnement')]
    #[Ignore]
    private Collection $options;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
        $this->options = new ArrayCollection();
    }
    public function __toString(): string
    {
        return $this->nom;
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

    public function getTarif(): ?string
    {
        return $this->tarif;
    }

    public function setTarif(string $tarif): static
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    public function addSubscriber(User $subscriber): static
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers->add($subscriber);
            $subscriber->setAbonnement($this);
        }

        return $this;
    }

    public function removeSubscriber(User $subscriber): static
    {
        if ($this->subscribers->removeElement($subscriber)) {
            // set the owning side to null (unless already changed)
            if ($subscriber->getAbonnement() === $this) {
                $subscriber->setAbonnement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OptionsAbonnement>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(OptionsAbonnement $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->addAbonnement($this);
        }

        return $this;
    }

    public function removeOption(OptionsAbonnement $option): static
    {
        if ($this->options->removeElement($option)) {
            $option->removeAbonnement($this);
        }

        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
