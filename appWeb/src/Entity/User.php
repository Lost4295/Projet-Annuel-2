<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\EquatableInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_BAILLEUR = 'ROLE_BAILLEUR';
    public const ROLE_PRESTA = 'ROLE_PRESTA';
    public const ROLE_VOYAGEUR = 'ROLE_VOYAGEUR';
    public const ROLE_NON_USER = 'ROLE_NON_USER';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 80)]
    private ?string $nom = null;

    #[ORM\Column(length: 80)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastConnDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column]
    private ?bool $admin = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: 'blob', nullable: true)]
    private $avatar;

    #[ORM\Column]
    private ?string $phoneNumber;


    #[ORM\ManyToOne(inversedBy: 'subscribers')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Abonnement $abonnement = null;

    #[OneToMany(targetEntity: Commentaire::class, mappedBy: 'user')]
    private $commentaires;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'demandeur')]
    private Collection $tickets;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'resolveur')]
    private Collection $ticketsAttribues;

    /**
     * @var Collection<int, Location>
     */
    #[ORM\OneToMany(targetEntity: Location::class, mappedBy: 'locataire', cascade: ['persist', 'remove'])]
    private Collection $locations;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $notes;

    /**
     * @var Collection<int, Warning>
     */
    #[ORM\OneToMany(targetEntity: Warning::class, mappedBy: 'user')]
    private Collection $warnings;

    #[ORM\Column(options: ["default"=> false])]
    private ?bool $isBanned = false;

    /**
     * @var Collection<int, Fichier>
     */
    #[ORM\OneToMany(targetEntity: Fichier::class, mappedBy: 'user')]
    private Collection $documents;


    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'user')]
    private Collection $devis;


    public function __construct()
    {
        $this->creationDate = new \DateTime();
        $this->roles = [self::ROLE_USER];
        $this->isVerified = false;
        $this->commentaires = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->ticketsAttribues = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->warnings = new ArrayCollection();
        $this->isBanned = false;
        $this->documents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFullName();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getLastConnDate(): ?\DateTimeInterface
    {
        return $this->lastConnDate;
    }

    public function setLastConnDate(?\DateTimeInterface $lastConnDate): static
    {
        $this->lastConnDate = $lastConnDate;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }



    public function isAdmin(): ?bool
    {
        if ($this->admin){
            $this->grantAdmin();
        } else {
            $this->removeAdmin();
        }
        return $this->admin;
    }

    public function setAdmin(bool $admin): static
    {
        $this->admin = $admin;
        if ($admin) {
            $this->grantAdmin();
        } else {
            $this->removeAdmin();
        }
        return $this;
    }


    public function grantAdmin(): static
    {
        $roles =  $this->getRoles();
        $roles[] = "ROLE_ADMIN";
        $this->setRoles($roles);
        return $this;
    }

    public function removeAdmin(): static
    {
        $roles =  $this->getRoles();
        $roles = array_diff($roles, ["ROLE_ADMIN"]);
        $this->setRoles($roles);
        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getPrenom() . ' ' . $this->getNom();
    }

    public function getInitials(): string
    {
        return strtoupper(substr($this->getPrenom(), 0, 1) . substr($this->getNom(), 0, 1));
    }

    /**
     * Get the value of avatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set the value of avatar
     *
     * @return  self
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }


    /**
     * Get the value of phoneNumber
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the value of phoneNumber
     *
     * @return  self
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAbonnement(): ?Abonnement
    {
        return $this->abonnement;
    }

    public function setAbonnement(?Abonnement $abonnement): static
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    public function getCommentaires()
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUser() === $this) {
                $commentaire->setUser(null);
            }
        }

        return $this;
    }

    public static function getPossibleRoles(): array
    {
        return [
            'user' => self::ROLE_USER,
            'admin' => self::ROLE_ADMIN,
            "baill" => self::ROLE_BAILLEUR,
            "prestataire" => self::ROLE_PRESTA,
            "voyageur" => self::ROLE_VOYAGEUR,
            "nouser" => self::ROLE_NON_USER
        ];
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setDemandeur($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getDemandeur() === $this) {
                $ticket->setDemandeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicketsAttribues(): Collection
    {
        return $this->ticketsAttribues;
    }

    public function addTicketsAttribue(Ticket $ticketsAttribue): static
    {
        if (!$this->ticketsAttribues->contains($ticketsAttribue)) {
            $this->ticketsAttribues->add($ticketsAttribue);
            $ticketsAttribue->setResolveur($this);
        }

        return $this;
    }

    public function removeTicketsAttribue(Ticket $ticketsAttribue): static
    {
        if ($this->ticketsAttribues->removeElement($ticketsAttribue)) {
            // set the owning side to null (unless already changed)
            if ($ticketsAttribue->getResolveur() === $this) {
                $ticketsAttribue->setResolveur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): static
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setLocataire($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): static
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getLocataire() === $this) {
                $location->setLocataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Warning>
     */
    public function getWarnings(): Collection
    {
        return $this->warnings;
    }

    public function addWarning(Warning $warning): static
    {
        if (!$this->warnings->contains($warning)) {
            $this->warnings->add($warning);
            $warning->setUser($this);
            if (count($this->warnings) >= 3 ){
                $this->setBanned(true);
            }
        }

        return $this;
    }

    public function removeWarning(Warning $warning): static
    {
        if ($this->warnings->removeElement($warning)) {
            // set the owning side to null (unless already changed)
            if ($warning->getUser() === $this) {
                $warning->setUser(null);
            }
        }

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setBanned(bool $isBanned): static
    {
        $this->isBanned = $isBanned;
        return $this;
    }

    /**
     * @return Collection<int, Fichier>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Fichier $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setUser($this);
        }

        return $this;
    }

    public function removeDocument(Fichier $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getUser() === $this) {
                $document->setUser(null);
            }
        }

        return $this;
    }

    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevis(Devis $devis): static
    {
        if (!$this->devis->contains($devis)) {
            $this->devis->add($devis);
            $devis->setUser($this);
        }

        return $this;
    }

    public function removeDevis(Devis $devis): static
    {
        if ($this->devis->removeElement($devis)) {
            // set the owning side to null (unless already changed)
            if ($devis->getUser() === $this) {
                $devis->setUser(null);
            }
        }

        return $this;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword() || $this->email !== $user->getEmail()) {
            return false;
        }

        return true;
    }
}
