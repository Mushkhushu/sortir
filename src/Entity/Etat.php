<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    const ETAT_CREEE    = 1;
    const ETAT_OUVERTE  = 2;
    const ETAT_CLOTUREE = 3;
    const ETAT_EN_COURS = 4;
    const ETAT_PASSEE   = 5;
    const ETAT_ANNULEE  = 6;
    // Je l'ai créé car vous utiliser l'état archivée mais normalement l'état archivée n'existe pas.
    // Et oui cela nous fait perdre des informations sur les sorties archivée on ne sait plus si elles ont été annulée ou autres.
    // Normalement archivée est soit une donnée supplémentaire à ajouter soit juste un filtre sur les sorties de plus d'un mois.
    const ETAT_ARCHIVEE = 7;

    #[ORM\Id]
    // Ici j'ai supprimé le generated value car vu que dans le code vous utilisiez des ID en dur j'ai mis des ID en dur dans mes fixtures
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    // Vous avez à chaque fois créé les collections inverse des relations mais la plus part du temps c'est inutile
    // Ici on ne récupérera jamais toutes les sorties d'un etat spécifique donc inutile d'avoir la collections.
    // on le voit au simple fait que supprimer ce code n'a pas du tout planté l'appli ce n'était donc pas utilisé.
    // C'est pareil pour les entité suivante j'ai supprimé toute les collections, des relations inversé, inutiles.

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }
}
