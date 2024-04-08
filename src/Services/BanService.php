<?php

namespace App\Services;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BanService
{
    public function __construct(private EntityManagerInterface $entityManager, private TokenStorageInterface $tokenStorage)
    {
    }

    //bannir un user
    public function banUser(User $user): void
    {
        $user->setActive(false);
        //le bannir pour 5 jours
        $banExpirationDate = new DateTimeImmutable('+5 days');
        $user->setBanExpirationDate($banExpirationDate);
        //le déconnecter
        if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser() === $user) {
            $this->tokenStorage->setToken(null); // Invalidate the user's session
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    //annuler le bannissement d'un user
    public function unbanUser(User $user): void
    {
        $user->setActive(true);
        $user->setBanExpirationDate(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    //vérifier si un user est ban ou non selon la date
    public function isUserBanned(User $user): bool
    {
        $now = new DateTimeImmutable();
        //si le user est toujours en ban
        if (!$user->isActive() && $user->getBanExpirationDate() >= $now) {
            return true;
        }
        //si le user n'est plus en ban (il faudra le déban)
        elseif (!$user->isActive() && $user->getBanExpirationDate() <= $now) {
            $this->unbanUser($user);
            return false;
        }
        return false;
    }
    public function updateUserBanStatus(User $user, bool $isActive): void
    {
        if (!$isActive) {
            // Si l'utilisateur est désactivé, bannir avec la date d'expiration appropriée
            $this->banUser($user);
        } else {
            // Si l'utilisateur est activé, annuler le bannissement
            $this->unbanUser($user);
        }
    }
}