<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Services\BanService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path: 'user/', name: 'user/')]
class UserController extends AbstractController
{
    public function getUsersInfos(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userName = $user?->getUsername();

        return $this->render('base.html.twig', [
            'username' => $userName, 'user' => $user
        ]);
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, BanService $banService): Response
    {
        $usersFromRepo = $userRepository->findAll();
        $bannedUsers = [];
        $users = [];
        foreach ($usersFromRepo as $user) {
            if ($user->isActive() === false) {
                $bannedUsers[] = $user;
            } else  {
                $users[] = $user;
            }
        }

        //dd($bannedUsers);
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'bannedUsers' => $bannedUsers,
        ]);
    }

    // Normalement il n'y a que les admins qui peuvent créer des users
    #[Route('new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user/index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('show/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('profile', name: 'profile', methods: ['GET'])]
    public function profile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        /** @var User $user */
        $user = $this->getUser();
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
            if (!empty($formData->getSite())) {
                $user->setSite($formData->getSite());
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
                    $picturePath = $this->getParameter('picture_dir') . '/' . $user->getPicture();
                    if (file_exists($picturePath)) {
                        unlink($picturePath);
                    }
                }
                $user->setPicture($fileName);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le profil a été mis à jour avec succès !');
            return $this->redirectToRoute('user/index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('change_password', name: 'change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur est authentifié
        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new \LogicException('Vous devez être authentifié pour accéder à cette page.');
        }

        $form = $this->createForm(EditPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            // Vérifie que le mot de passe actuel est correct
            if (!$passwordHasher->isPasswordValid($user, $formData['current_password'])) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('user/change_password');
            }

            // Hachage du nouveau mot de passe
            $newPassword = $formData['new_password'];
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès !');
            return $this->redirectToRoute('user/profile', ['id' => $user->getId()]);
        }

        return $this->render('user/changePassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    // un utilisateur est pas sensé pouvoir supprimer son compte y'a que les admins qui le peuvent
    #[Route('delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        $this->addFlash('success', 'utilisateur supprimé avec succès !');
        return $this->redirectToRoute('user/index', [], Response::HTTP_SEE_OTHER);
    }

}

