<?php

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
class Email
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $expeditor = null;

    #[ORM\Column(length: 255)]
    private ?string $reciever = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpeditor(): ?string
    {
        return $this->expeditor;
    }

    public function setExpeditor(string $expeditor): static
    {
        $this->expeditor = $expeditor;

        return $this;
    }

    public function getReciever(): ?string
    {
        return $this->reciever;
    }

    public function setReciever(string $reciever): static
    {
        $this->reciever = $reciever;

        return $this;
    }
}
