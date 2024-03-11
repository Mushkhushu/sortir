<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path: 'user/', name: 'user_')]
class UserController extends AbstractController
{
    public function getUsersInfos(Security $security): Response
    {
        $user = $security->getUser();
        $userName = $user?->getUsername();

        return $this->render('base.html.twig', [
            'username' => $userName, 'user' => $user
        ]);
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('show{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('profile', name: 'profile', methods: ['GET'])]
    public function profile(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Security $security,Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $security->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            if (!empty($formData->getUsername())) {
               $user->setUsername($formData->getUsername());
            }
            if (!empty($formData->getFirstName())) {
                $user->setFirstName($formData->getFirstName());
            }
            if (!empty($formData->getlastName())) {
                $user->setLastName($formData->getLastName());
            }
            if (!empty($formData->getPhoneNumber())) {
                $user->setPhoneNumber($formData->getPhoneNumber());
            }
            if (!empty($formData->getMail())) {
                $user->setMail($formData->getMail());
            }
            if ($form->get('picture')->getData() instanceof UploadedFile) {
                $pictureFile = $form->get('picture')->getData();
                $fileName = $slugger->slug($user->getUsername()) . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                $pictureFile->move($this->getParameter('picture_dir'), $fileName);
                if (!empty($user->getPicture())) {
                    $picturePath= $this->getParameter('picture_dir') . '/' . $user->getPicture();
                    if (file_exists($picturePath)) {
                        unlink($picturePath);
                    }
                }
                $user->setPicture($fileName);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le profil a été mis à jour avec succès !');
            return $this->redirectToRoute('user_show');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
