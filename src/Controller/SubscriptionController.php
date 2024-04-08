<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SubscriptionController extends AbstractController
{

    #[Route('/register/{id}', name: 'register_to_sortie')]
    public function registerToSortie(EntityManagerInterface $entityManager, Sortie $sortie): Response
    {
        $error = false;
        $user = $this->getUser();

        // Vérifier l'état de la sortie (elle doit être ouverte)
        if ($sortie->getEtat()->getId() !== Etat::ETAT_OUVERTE) {
            $this->addFlash('warning', 'Vous ne pouvez pas vous inscrire à cette sortie, elle n\'est pas ouverte.');
            $error = true;
        }

        // Quelle est le but de cette vérif ? checker si l'utilisateur est connecté ?
        // if (!$user instanceof User) {
        //     throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de la classe User.');
        // }
        // Si oui alors on peut faire avec l'attribut au dessus de la fonction ou classe: "#[IsGranted('ROLE_USER')]"

        // Vérifier si l'utilisateur est déjà inscrit à la sortie
        if ($sortie->getParticipants()->contains($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
            $error = true;
        }
        // Vérifier si la date limite est dépassée
        if ($sortie->getDateLimite() < new \DateTime()) {
            $this->addFlash('danger', 'La date limite pour s\'inscrire à cette sortie est dépassée.');
            $error = true;
        }

        // Vérifier si le nombre maximal de participants est atteint
        if ($sortie->getParticipants()->count() >= $sortie->getNbrPersonne()) {
            $this->addFlash('danger', 'Le nombre maximal de participants à cette sortie est atteint.');
            $error = true;
        }

        if(!$error) {
            // Ajouter l'utilisateur comme participant à la sortie
            $sortie->addParticipant($user);

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            $this->addFlash('success', 'vous avez été ajouté à la sortie');
        }

        // Rediriger vers la page des sorties
        return $this->redirectToRoute('sorties/show', ['id' => $sortie->getId()]);
    }


    #[Route('/unsubscribe/{id}', name: 'unsubscribe_to_sortie')]
    public function unsubscribeToSortie(EntityManagerInterface $entityManager, Sortie $sortie): Response
    {
        $user = $this->getUser();

        if (!$sortie->getParticipants()->contains($user)) {
            $this->addFlash('warning', "Vous n'êtes pas inscrit à cette sortie.");
        } else {
            $sortie->removeParticipant($user);
            $entityManager->flush();
            $this->addFlash('success', 'vous avez été viré de la sortie');
        }

        return $this->redirectToRoute('sorties/show', ['id' => $sortie->getId()]);
    }
}
