<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortiesRepository::class)]
class Sorties
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(value: 1, message: "La duree doit être positive.")]
    private ?int $duree = 0;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(value: 1, message: "selectionner au moins un participant")]
    private ?int $nbrPersonne = 0;

    #[ORM\Column(length: 255)]
    private ?string $note = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateLimite = null;

    #[ORM\ManyToOne(inversedBy: 'createdEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizator = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'participatingEvents')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    #[ORM\ManyToOne(inversedBy: 'sorties',targetEntity: Lieu::class, cascade: ['persist'])]
    private ?Lieu $Lieu = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }


    public function getNbrPersonne(): ?int
    {
        return $this->nbrPersonne;
    }

    public function setNbrPersonne(int $nbrPersonne): static
    {
        $this->nbrPersonne = $nbrPersonne;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDateLimite(): ?\DateTimeImmutable
    {
        return $this->dateLimite;
    }

    public function setDateLimite(\DateTimeImmutable $dateLimite): static
    {
        $this->dateLimite = $dateLimite;

        return $this;
    }

    public function getOrganizator(): ?User
    {
        return $this->organizator;
    }

    public function setOrganizator(?User $organizator): static
    {
        $this->organizator = $organizator;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addParticipatingEvent($this);
        }
        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeParticipatingEvent($this);
        }

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }


    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->Lieu;
    }

    public function setLieu(?Lieu $Lieu): static
    {
        $this->Lieu = $Lieu;

        return $this;
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->getNom();}

}
