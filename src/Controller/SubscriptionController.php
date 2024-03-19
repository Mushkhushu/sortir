<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Entity\User;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SubscriptionController extends AbstractController
{


    #[Route('/register/{id}', name: 'register_to_sortie')]
    public function registerToSortie(EntityManagerInterface $entityManager, SortiesRepository $sortiesRepository, Security $security, Request $request, int $id): Response
    {
        $user = $security->getUser();
        $sortie = $sortiesRepository->find($id);
        //Vérifier l'état de la sortie (elle doit être ouverte)
        $etatSortie = $sortie->getEtat()->getId();
        if ($etatSortie !== 2) {
            $this->addFlash('warning', 'Vous ne pouvez pas vous inscrire à cette sortie, elle n\'est pas ouverte.');
            return $this->redirectToRoute('sorties/show', ['id' => $id]);
        }
        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de la classe User.');
        }
        // Vérifier si l'utilisateur est déjà inscrit à la sortie
        if ($sortie->getParticipants()->contains($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
            return $this->redirectToRoute('sorties/show', ['id' => $id]);
        }
        // Vérifier si la date limite est dépassée
        if ($sortie->getDateLimite() < new \DateTime()) {
            $this->addFlash('danger', 'La date limite pour s\'inscrire à cette sortie est dépassée.');
            return $this->redirectToRoute('sorties/show', ['id' => $id]);
        }

        // Vérifier si le nombre maximal de participants est atteint
        if ($sortie->getParticipants()->count() >= $sortie->getNbrPersonne()) {
            $this->addFlash('danger', 'Le nombre maximal de participants à cette sortie est atteint.');
            return $this->redirectToRoute('sorties/show', ['id' => $id]);
        }
        // Ajouter l'utilisateur comme participant à la sortie
        $sortie->addParticipant($user);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Rediriger vers la page des sorties
        $this->addFlash('success', 'vous avez été ajouté à la sortie');
        return $this->redirectToRoute('sorties/show', ['id' => $id]);

    }
    #[Route('/unsubscribe/{id}', name: 'unsubscribe_to_sortie')]
    public function unsubscribeToSortie(EntityManagerInterface $entityManager, SortiesRepository $sortiesRepository, Security $security, Request $request, int $id): Response
    {
        $user = $security->getUser();
        $sortie = $sortiesRepository->find($id);
        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur connecté n\'est pas une instance de la classe User.');
        }

        $sortie->removeParticipant($user);
        $entityManager->flush();
        $this->addFlash('success', 'vous avez été viré de la sortie');
        return $this->redirectToRoute('sorties/show', ['id' => $id]);
        }
    }


