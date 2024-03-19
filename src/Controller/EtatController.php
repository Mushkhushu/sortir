<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sorties;
use App\Form\CancelationType;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route(path: '/etat/', name: 'etat_')]
class EtatController extends AbstractController
{
    #[Route('index', name: 'index')]
    public function index(): Response
    {
        return $this->render('etat/index.html.twig', [
            'controller_name' => 'EtatController',
        ]);
    }
    #[Route('cancel{id}', name: 'cancel', methods: ['GET','POST'])]
    public function cancel(Request $request, SortiesRepository $sortiesRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $form = $this->createForm(CancelationType::class);
        $form->handleRequest($request);
        $sorty = $sortiesRepository->find($id);
        if($sorty->getEtat()->getId() == 2){
            $this->addFlash('danger', 'Cette sortie est ouverte et ne peut pas être annulée !');
            return $this->redirectToRoute('sorties/show', ['id' => $id]);
        }
        $etat = $entityManager->getRepository(Etat::class)->find(6);
        if($form->isSubmitted() && $form->isValid()){
            $sorty->setEtat($etat);
            $entityManager->persist($sorty);
            $entityManager->flush();
            $this->addFlash('danger', 'La sortie est désormais annulée !');
            return $this->redirectToRoute('sorties/show', ['id' => $id]);
        }
        return $this->render('etat/cancel.html.twig', [
            'etat' => $etat,
            'sorty' => $sorty,
            'form' => $form,
        ]);
    }

}
