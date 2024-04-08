<?php

namespace App\Controller;



use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\CancelationType;
use App\Form\LieuType;
use App\Form\RechercheType;
use App\Form\SortiesType;
use App\Repository\SortieRepository;
use App\Services\EtatUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/sorties')]
class SortiesController extends AbstractController
{
    #[Route('/', name: 'sorties/index', methods: ['GET', 'POST'])]
    public function index(Request $request, EtatUpdater $etatUpdater, SortieRepository $sortiesRepository): Response
    {
        $formFilter = $this->createForm(RechercheType::class);
        $formFilter->handleRequest($request);
        // On ne veut jamais retourner toutes les sorties.
        // On ne doit pas voir les sorties archivée.
        // On ne doit pas voir les sorties créée par d'autre utilisateur que nous.
        // On ne doit pas voir les sorties annulée dont on n'est ni orga ni participant.
        $sorties = [];

        if ($formFilter->isSubmitted() && $formFilter->isValid()) {
            $data = $formFilter->getData();
            $user = $this->getUser();
            $sorties = $sortiesRepository->findByFilter($data, $user);
            // Ici pas besoin de render le même code qu'en dessous il faut adapter le code pour éviter de se répéter.
        } else {
            // utilisation du else pour ne pas avoir à faire 2 requêtes SQL si on est dans le cas de l'utilisation des filtres.
            $sorties = $sortiesRepository->findAll();
        }

        $etatUpdater->updateEtatsSorties($sorties);

        return $this->render('sorties/index.html.twig', [
            'sorties' => $sorties,
            'formFilter' => $formFilter
        ]);
    }

    #[Route('/new', name: 'sorties/new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $lieu= new Lieu();
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganizator($this->getUser());
            $sortie->setEtat($entityManager->getReference(Etat::class, Etat::ETAT_CREEE));
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Nouvelle sortie ajoutée avec succès !');
            return $this->redirectToRoute('sorties/index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sorties/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
            'formLieu' => $formLieu
        ]);
    }

    #[Route('/{id}', name: 'sorties/show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sorties/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'sorties/edit', methods: ['GET','POST'])]
    public function edit(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SortiesType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie modifiée avec succès !');
            return $this->redirectToRoute('sorties/index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sorties/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    // J'ai mis la méthode cancel ici elle était dans EtatController mais c'est bien une SORTIE qu'on cancel pas un état
    // C'est donc une modif d'une sortie donc devrait être dans le controller de sortie.
    // J'ai supprimé le controller état qui ne servait donc à rien.
    #[Route('/{id}/cancel', name: 'sorties/cancel', methods: ['GET', 'POST'])]
    public function cancel(Request $request, EntityManagerInterface $entityManager, Sortie $sortie): Response
    {
        $form = $this->createForm(CancelationType::class);
        $form->handleRequest($request);

        if ($sortie->getEtat()->getId() === Etat::ETAT_CREEE) {
            $this->addFlash('danger', 'Cette sortie est ouverte et ne peut pas être annulée !');
            return $this->redirectToRoute('sorties/show', ['id' => $sortie->getId()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat($entityManager->getReference(Etat::class, Etat::ETAT_ANNULEE));
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('danger', 'La sortie est désormais annulée !');
            return $this->redirectToRoute('sorties/show', ['id' => $sortie->getId()]);
        }

        return $this->render('sorties/cancel.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'sorties/delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Sortie supprimée avec succès !');
        return $this->redirectToRoute('sorties/index', [], Response::HTTP_SEE_OTHER);
    }
}
