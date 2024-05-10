<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    public const TYPE_EPIC = 1;
    public const TYPE_TASK = 2;
    public const TYPE_STORY = 3;
    public const TYPE_BUG = 4;
    public const TYPE_SUBTASK = 5;
    public const TYPE_LIST = [
        self::TYPE_EPIC => "epic",
        self::TYPE_TASK => "task",
        self::TYPE_STORY => "story",
        self::TYPE_BUG => "bug",
        self::TYPE_SUBTASK => "subtask"
    ];

    public const CATEGORY_TECHNIQUE = 1;
    public const CATEGORY_FONCTIONNEL = 2;
    public const CATEGORY_DEMANDE = 3;
    public const CATEGORY_INCIDENT = 4;
    public const CATEGORY_AUTRE = 5;
    public const CATEGORY_LIST = [
        self::CATEGORY_TECHNIQUE => "technique",
        self::CATEGORY_FONCTIONNEL => "fonctionnel",
        self::CATEGORY_DEMANDE => "demande",
        self::CATEGORY_INCIDENT => "incident",
        self::CATEGORY_AUTRE => "autre"
    ];

    public const STATUS_NOUVEAU = 1;
    public const STATUS_EN_COURS = 2;
    public const STATUS_RESOLU = 3;
    public const STATUS_FERME = 4;
    public const STATUS_EN_ATTENTE = 5;
    public const STATUS_REJETE = 6;
    public const STATUS_LIST = [
        self::STATUS_NOUVEAU => "nouveau",
        self::STATUS_EN_COURS=>"encours",
        self::STATUS_RESOLU=>"resolu",
        self::STATUS_FERME=>"ferme",
        self::STATUS_EN_ATTENTE=>"enattente",
        self::STATUS_REJETE=>"rejete",
    ];

    public const PRIORITY_BASSE = 1;
    public const PRIORITY_NORMALE = 2;
    public const PRIORITY_HAUTE = 3;
    public const PRIORITY_URGENTE = 4;
    public const PRIORITY_LIST = [
        self::PRIORITY_BASSE=>"basse",
        self::PRIORITY_NORMALE=>"normale",
        self::PRIORITY_HAUTE=>"haute",
        self::PRIORITY_URGENTE=>"urgente",
    ];

    public const URGENCE_BASSE = 1;
    public const URGENCE_NORMALE = 2;
    public const URGENCE_HAUTE = 3;
    public const URGENCE_URGENTE = 4;
    public const URGENCE_LIST = [
        self::URGENCE_BASSE=>"basse",
        self::URGENCE_NORMALE=>"normale",
        self::URGENCE_HAUTE=>"haute",
        self::URGENCE_URGENTE=>"urgente",
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOuverture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFermeture = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $demandeur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastUpdateDate = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $category = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $priority = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, Fichier>
     */
    #[ORM\OneToMany(targetEntity: Fichier::class, mappedBy: 'ticket')]
    private Collection $pj;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $urgence = null;

    #[ORM\ManyToOne(inversedBy: 'ticketsAttribues')]
    private ?User $resolveur = null;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->pj = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->dateOuverture;
    }

    public function setDateOuverture(\DateTimeInterface $dateOuverture): static
    {
        $this->dateOuverture = $dateOuverture;

        return $this;
    }

    public function getDemandeur(): ?User
    {
        return $this->demandeur;
    }

    public function setDemandeur(?User $demandeur): static
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getLastUpdateDate(): ?\DateTimeInterface
    {
        return $this->lastUpdateDate;
    }

    public function setLastUpdateDate(\DateTimeInterface $lastUpdateDate): static
    {
        $this->lastUpdateDate = $lastUpdateDate;

        return $this;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(int $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
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
            $pj->setTicket($this);
        }

        return $this;
    }

    public function removePj(Fichier $pj): static
    {
        if ($this->pj->removeElement($pj)) {
            // set the owning side to null (unless already changed)
            if ($pj->getTicket() === $this) {
                $pj->setTicket(null);
            }
        }

        return $this;
    }

    public function getUrgence(): ?int
    {
        return $this->urgence;
    }

    public function setUrgence(int $urgence): static
    {
        $this->urgence = $urgence;

        return $this;
    }

    public function getResolveur(): ?User
    {
        return $this->resolveur;
    }

    public function setResolveur(?User $resolveur): static
    {
        $this->resolveur = $resolveur;

        return $this;
    }

    /**
     * Get the value of dateFermeture
     */
    public function getDateFermeture(): ?\DateTimeInterface
    {
        return $this->dateFermeture;
    }

    /**
     * Set the value of dateFermeture
     *
     * @return  self
     */
    public function setDateFermeture($dateFermeture): static
    {
        $this->dateFermeture = $dateFermeture;

        return $this;
    }
}
