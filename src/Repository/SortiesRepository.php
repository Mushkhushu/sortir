<?php

namespace App\Repository;

use App\Entity\Sorties;

use Composer\XdebugHandler\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Error;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @extends ServiceEntityRepository<Sorties>
 *
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }
    public function findByFilter(?string $date, ?string $nom,): array
    {
        $qb = $this->createQueryBuilder('s');

        if ($date) {
            $qb->andWhere('s.date = :date')
                ->setParameter('date', $date);
        }

        if ($nom) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', "%{$nom}%");
        }


        return $qb->getQuery()->getResult();
    }



    public function findByUser(?string $user, ?string $User,): array
    {
        if ($user) {
            return $this->createQueryBuilder('s')
                ->andWhere('s.organizator = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
        }
        if ($User) {
            return $this->createQueryBuilder('s')
                ->andWhere('s.user = :User')
                ->setParameter('User', $User)
                ->getQuery()
                ->getResult();

        }
        return [];
    }
    //    /**
    //     * @return Sorties[] Returns an array of Sorties objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sorties
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
