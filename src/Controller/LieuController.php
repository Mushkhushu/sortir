<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use App\Services\MapBoxHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lieu')]
class LieuController extends AbstractController
{
    #[Route('/', name: 'app_lieu_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,LieuRepository $lieuRepository,MapBoxHelper $mapBoxHelper): Response
    {
        foreach ($lieuRepository->findAll() as $lieu) {
            $coordinates= $mapBoxHelper->getAddressCoordinates($lieu->getRue(), $lieu->getVille()->getCodePostal(), $lieu->getVille()->getNom());
            $lieu->setLatitude($coordinates['lat']);
            $lieu->setLongitude($coordinates['lng']);
            $entityManager->persist($lieu);
            $entityManager->flush();
        }
        //hydrate les coordonnées reçues dans l'entité

        return $this->render('lieu/index.html.twig', [
            'lieus' => $lieuRepository->findAll(),
        ]);
    }
    #[Route('/lieu/new', name: 'app_lieu_new', methods: ['GET', 'POST'])]
    public function new(MapBoxHelper $mapBoxHelper, Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ville= $form->get('Ville')->getData();
            // Définir la ville pour le lieu
            $lieu->setVille($ville);
            $rue = $form->get('rue')->getData();
            $lieu->setRue($rue);
            $coordinates= $mapBoxHelper->getAddressCoordinates($lieu->getRue(), $lieu->getVille()->getCodePostal(), $lieu->getVille()->getNom());
            $lieu->setLatitude($coordinates['lat']);
            $lieu->setLongitude($coordinates['lng']);
            // Enregistrer le lieu en base de données
            $entityManager->persist($lieu);
            $entityManager->flush();
            // Rediriger vers la page de détail du lieu
            $this->addFlash('success', 'Nouveau lieu ajouté avec succès !');
            return $this->redirectToRoute('app_lieu_show',['id' => $lieu->getId()]);
        }

        return $this->render('lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_lieu_show', methods: ['GET'])]
    public function show(Lieu $lieu): Response
    {
        return $this->render('lieu/show.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lieu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Lieu modifié avec succès !');
            return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lieu_delete', methods: ['POST'])]
    public function delete(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lieu);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Lieu supprimé avec succès !');
        return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
    }
}
