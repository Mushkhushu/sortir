<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'Ce pseudo est déjà pris', errorPath: 'pseudo')]
#[UniqueEntity(fields: ['mail'], message: 'Ce mail est déjà pris', errorPath: 'mail')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const PWD_MIN_LGTH = 12;
    const PWD_MAX_LGTH = 4096;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[Assert\Length(max: 50, maxMessage: "Le pseudo doit faire moins de {{ limit }} caractères.")]
    #[Assert\Length(min: 3, minMessage: "Le pseudo doit faire plus de {{ limit }} caractères.")]
    #[ORM\Column(name: 'username', type: Types::STRING, length: 50, unique: true, nullable: true)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\Length(
        min: self::PWD_MIN_LGTH,
        minMessage: "Votre mot de passe doit être au minimum de {{ limit }} caractères",
        max: self::PWD_MAX_LGTH,
    )]
    #[Assert\PasswordStrength]
    #[Assert\NotCompromisedPassword]
    #[ORM\Column]
    private ?string $password = null;

    // J'ai juste regroupé les assert ensembles, c'est important de bien ranger le code
    // Genre tous les Asserts au dessus du ORM\Column ou en dessous mais il faut faire partout pareil
    // Si on change à chaque endroit de façon de faire se sera plus dur de relire le code plus tard
    #[Assert\Length(min: 3, minMessage: "Le Prenom doit faire plus de {{ limit }} caractères.")]
    #[Assert\Length(max: 50, maxMessage: "Le prenom doit faire moins de {{ limit }} caractères.")]
    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 50, nullable: true)]
    private ?string $firstName = null;

    // ici on peut regrouper en un seul assert si on veut rien d'obligatoir.
    #[Assert\Length(
        min: 3,
        minMessage: "Le Nom doit faire plus de {{ limit }} caractères.",
        max: 50,
        maxMessage: "Le nom doit faire moins de {{ limit }} caractères."
    )]
    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 50, nullable: true)]
    private ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Length(exactly: 12)]
    #[ORM\Column(length: 12, nullable: true)]
    private ?string $phoneNumber = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre adresse mail.')]
    #[Assert\Email(message: 'Veuillez renseigner une adresse mail valide')]
    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private ?string $mail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[Assert\NotNull]
    #[Assert\Type(type: Site::class)]
    #[ORM\ManyToOne]
    private ?Site $site = null;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    private Collection $groupe;

    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'createBy', orphanRemoval: true)]
    private Collection $groupesPrivé;

    #[ORM\Column(options: ["default" => true])]
    private ?bool $active = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $banExpirationDate = null;

    public function __construct()
    {
        $this->groupe = new ArrayCollection();
        $this->groupesPrivé = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function setUser(mixed $user)
    {
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroupe(): Collection
    {
        return $this->groupe;
    }

    public function addGroupe(Group $groupe): static
    {
        if (!$this->groupe->contains($groupe)) {
            $this->groupe->add($groupe);
            $groupe->addUser($this);
        }

        return $this;
    }

    public function removeGroupe(Group $groupe): static
    {
        if ($this->groupe->removeElement($groupe)) {
            $groupe->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroupesPrivé(): Collection
    {
        return $this->groupesPrivé;
    }

    public function addGroupesPriv(Group $groupesPriv): static
    {
        if (!$this->groupesPrivé->contains($groupesPriv)) {
            $this->groupesPrivé->add($groupesPriv);
            $groupesPriv->setCreateBy($this);
        }

        return $this;
    }

    public function removeGroupesPriv(Group $groupesPriv): static
    {
        if ($this->groupesPrivé->removeElement($groupesPriv)) {
            // set the owning side to null (unless already changed)
            if ($groupesPriv->getCreateBy() === $this) {
                $groupesPriv->setCreateBy(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $isActive): static
    {
        $this->active = $isActive;

        return $this;
    }

    public function getBanExpirationDate(): ?DateTimeImmutable
    {
        return $this->banExpirationDate;
    }

    public function setBanExpirationDate(?DateTimeImmutable $banExpirationDate): static
    {
        $this->banExpirationDate = $banExpirationDate;

        return $this;
    }
}
