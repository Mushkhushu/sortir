<?php

namespace App\Services;

use App\Entity\Etat;
use App\Entity\Sorties;
use Doctrine\ORM\EntityManagerInterface;

class EtatUpdater
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateEtat(Sorties $sortie, EntityManagerInterface $entityManager): void
    {
        $dateDebutMoinsUneSemaine = (clone $sortie->getDate())->modify('-1 week');
        $dateFinEvent = (clone $sortie->getDate())->modify('+' . $sortie->getDuree() . ' hours');
        $datePlusUnMois = (clone $sortie->getDate())->modify('+1 month');
        $now = new \DateTimeImmutable();

        // Passer de "créée" à "ouverte" si event dans une semaine
        if ($dateDebutMoinsUneSemaine <= $now && $sortie->getEtat()->getId() === 1) {
            $sortie->setEtat($entityManager->getReference(Etat::class, 2));
        }

        // Passer de "ouverte" à "clôturée" si dateLimite dépassée
        if ($sortie->getDateLimite() <= $now && $sortie->getEtat()->getId() === 2) {
            $sortie->setEtat($entityManager->getReference(Etat::class, 3));
        }

        // Passer de "clôturée" à "activité en cours" si date = now
        if ($sortie->getDate() <= $now && $sortie->getEtat()->getId() === 3) {
            $sortie->setEtat($entityManager->getReference(Etat::class, 4));
        }

        // Passer de "activité en cours" à "passée" si dateEvent + durée = now
        if ($dateFinEvent <= $now && $sortie->getEtat()->getId() === 4) {
            $sortie->setEtat($entityManager->getReference(Etat::class, 5));
        }

        // Passer de "passée" à "archivée" si dateEvent + 1 mois = now
        if ($datePlusUnMois <= $now && $sortie->getEtat()->getId() === 5) {
            $sortie->setEtat($entityManager->getReference(Etat::class, 7));
        }

        // L'état "annulée" se gère manuellement ailleurs
    }
}