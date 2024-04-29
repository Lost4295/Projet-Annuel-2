<?php

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


// TODO : Make a command to check if emails need to be sent
#[ORM\Entity(repositoryClass: EmailRepository::class)]
class Email
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $destinataire = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cc = null;

    #[ORM\OneToMany(targetEntity: Fichier::class, mappedBy: 'email')]
    private Collection $pj;

    #[ORM\Column(length: 255)]
    private ?string $object = null;

    #[ORM\Column]
    private ?bool $isAutomatic = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function __construct()
    {
        $this->pj = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->object;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    public function setDestinataire(string $destinataire): static
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getCc(): ?string
    {
        return $this->cc;
    }

    public function setCc(?string $cc): static
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * @return Collection<int, Fichier>
     */
    public function getPj(): Collection
    {
        return $this->pj;
    }

    public function addPj(Fichier $pj): static
    {
        if (!$this->pj->contains($pj)) {
            $this->pj->add($pj);
            $pj->setEmail($this);
        }

        return $this;
    }

    public function removePj(Fichier $pj): static
    {
        if ($this->pj->removeElement($pj)) {
            // set the owning side to null (unless already changed)
            if ($pj->getEmail() === $this) {
                $pj->setEmail(null);
            }
        }

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): static
    {
        $this->object = $object;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isAutomatic(): ?bool
    {
        return $this->isAutomatic;
    }

    public function setAutomatic(bool $isAutomatic): static
    {
        $this->isAutomatic = $isAutomatic;

        return $this;
    }

}
