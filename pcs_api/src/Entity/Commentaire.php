<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
#[ApiResource]
class Commentaire
{

    const BLOG = 0;
    const APPART = 1;
    const SERVICE = 2;
    const PRODUIT = 3;
    const TICKET = 4;

    private const TYPE_LIST = [
        'blog' => self::BLOG,
        'appartement' => self::APPART,
        'service' => self::SERVICE,
        'produit' => self::PRODUIT,
        'ticket' => self::TICKET
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'commentaires')]
    private $user;

    #[ORM\Column(type: Types::SMALLINT)]
    private $type;

    #[ORM\Column(type: Types::INTEGER)]
    private $entityId;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }


    public function setEntityId(int $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }


    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public static function getTypes(): array
    {
        return self::TYPE_LIST;
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
}
