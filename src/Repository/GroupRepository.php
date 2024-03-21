<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }


    public function findByIdForGroup(User $user): array
    {
        $qb = $this->createQueryBuilder('g');

        $qb->innerJoin('g.users', 'p')
            ->andWhere('p.id = :userId');

        // Ajouter une condition pour récupérer les groupes créés par l'utilisateur
        $qb->orWhere('g.createBy = :userId');

        // Définir le paramètre :userId
        $qb->setParameter('userId', $user->getId());

        // Récupérer les résultats
        return $qb->getQuery()->getResult();

        ;
    }

//    public function findOneBySomeField($value): ?Group
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
