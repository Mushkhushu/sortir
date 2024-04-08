<?php

namespace App\Services;

use App\Entity\Etat;
use App\Entity\Sortie;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class EtatUpdater
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     *
     * @param Sortie[] $sorties
     * @return void
     * @throws ORMException
     */
    public function updateEtatsSorties(array $sorties): void
    {
        foreach ($sorties as $sortie) {
            $this->updateEtat($sortie);
        }

        // On ne flush pas à chaque edition pour rendre les requetes plus rapide on flush qu'a la fin
        // Cela va lancer une seule requête sql pour toute la modif
        $this->em->flush();
    }

    public function updateEtat(Sortie $sortie): void
    {
        $dateDebutMoinsUneSemaine = (clone $sortie->getDate())->modify('-1 week');
        $dateFinEvent = (clone $sortie->getDate())->modify('+' . $sortie->getDuree() . ' hours');
        $datePlusUnMois = (clone $sortie->getDate())->modify('+1 month');
        $now = new DateTimeImmutable();

        // C'est que quand l'organisateur publie la sortie qu'elle devient 'ouverte'

        // Passer de "créée" à "ouverte" si event dans une semaine
        if ($dateDebutMoinsUneSemaine <= $now && $sortie->getEtat()->getId() === Etat::ETAT_CREEE) {
            $sortie->setEtat($this->em->getReference(Etat::class, Etat::ETAT_OUVERTE));
        }

        // Passer de "ouverte" à "clôturée" si dateLimite dépassée
        if ($sortie->getDateLimite() <= $now && $sortie->getEtat()->getId() === Etat::ETAT_OUVERTE) {
            $sortie->setEtat($this->em->getReference(Etat::class, Etat::ETAT_CLOTUREE));
        }

        // Passer de "clôturée" à "activité en cours" si date = now
        if ($sortie->getDate() <= $now && $sortie->getEtat()->getId() === Etat::ETAT_CLOTUREE) {
            $sortie->setEtat($this->em->getReference(Etat::class, Etat::ETAT_EN_COURS));
        }

        // Passer de "activité en cours" à "passée" si dateEvent + durée = now
        if ($dateFinEvent <= $now && $sortie->getEtat()->getId() === Etat::ETAT_EN_COURS) {
            $sortie->setEtat($this->em->getReference(Etat::class, Etat::ETAT_PASSEE));
        }

        // Passer de "passée" à "archivée" si dateEvent + 1 mois = now
        if ($datePlusUnMois <= $now && $sortie->getEtat()->getId() === Etat::ETAT_PASSEE) {
            $sortie->setEtat($this->em->getReference(Etat::class, Etat::ETAT_ARCHIVEE));
        }

        $this->em->persist($sortie);

        // L'état "annulée" se gère manuellement ailleurs
    }
}