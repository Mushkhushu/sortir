<?php

namespace App\Controller;



use App\Entity\Etat;
use App\Entity\Sorties;
use App\Form\SortiesType;
use App\Repository\SortiesRepository;
use App\Services\EtatUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/sorties')]
#[IsGranted('ROLE_USER')]
class SortiesController extends AbstractController
{
    #[Route('/', name: 'sorties/index', methods: ['POST', 'GET'])]
    public function index(EntityManagerInterface $entityManager, EtatUpdater $etatUpdater): Response
    {
        $sorties = $entityManager->getRepository(Sorties::class)->findAll();
        foreach ($sorties as $sorty) {
            $etatUpdater->updateEtat($sorty, $entityManager);
            $entityManager->persist($sorty);
            $entityManager->flush();
        }
        return $this->render('sorties/index.html.twig', [
            'sorties' => $sorties,
        ]);
    }
    #[Route('/filter', name: 'sorties/filter', methods: ['POST', 'GET'])]
    public function filter(Request $request, SortiesRepository $sortiesRepository): Response
    {
        $date = $request->query->get('date');
        $nom = $request->query->get('nom');

        $sorties = $sortiesRepository->findByFilter($date, $nom);
        return $this->render('sorties/index.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('/new', name: 'sorties/new', methods: ['GET', 'POST'])]
    public function new(Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sorty = new Sorties();
        $form = $this->createForm(SortiesType::class, $sorty, ['display_participants' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sorty->setOrganizator($security->getUser());
            $etat = $entityManager->getRepository(Etat::class)->find(1);
            $sorty->setEtat($etat);
            $entityManager->persist($sorty);
            $entityManager->flush();

            return $this->redirectToRoute('sorties/index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sorties/new.html.twig', [
            'sorty' => $sorty,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'sorties/show', methods: ['GET'])]
    public function show(Sorties $sorty): Response
    {
        return $this->render('sorties/show.html.twig', [
            'sorty' => $sorty,
        ]);
    }

    #[Route('/{id}/edit', name: 'sorties/edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sorties $sorty, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SortiesType::class, $sorty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('sorties/index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sorties/edit.html.twig', [
            'sorty' => $sorty,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'sorties/delete', methods: ['POST'])]
    public function delete(Request $request, Sorties $sorty, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sorty->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sorty);
            $entityManager->flush();
        }
        return $this->redirectToRoute('sorties/index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/filter', name: 'sorties/filter', methods: ['GET'])]
    public function filter(Request $request, SortiesRepository $sortiesRepository): Response
    {
        $date = $request->query->get('date');
        $nom = $request->query->get('nom');

        $sorties = $sortiesRepository->findByFilter($date, $nom);

        return $this->render('sorties/index.html.twig', [
            'sorties' => $sorties,
        ]);
    }
}
