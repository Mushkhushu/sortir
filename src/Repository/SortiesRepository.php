<?php

namespace App\Repository;

use App\Entity\Sorties;

use App\Entity\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function findByFilter(array $data, int $userID): array
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->leftJoin('s.participants', 'p')
            ->leftJoin('s.organizator', 'o');
        if (!empty($data['participants']) && empty($data['non_participants'])) {
            $qb->andWhere(':userID MEMBER OF s.participants')
                ->setParameter('userID', $userID);
        }
        if (!empty($data['non_participants']) && empty($data['participants'])) {
            $qb->andWhere(':userID NOT MEMBER OF s.participants')
                ->setParameter('userID', $userID);
        }
        if (!empty($data['organizator'])) {
            $qb->andWhere('s.organizator = :organizator')
                ->setParameter('organizator', $userID);
        }
        if (!empty($data['search'])) {
            $qb->andWhere('s.nom LIKE :search')
                ->setParameter('search', "%{$data['search']}%");
        }
        if (!empty($data['site'])) {
            $qb->andWhere('o.site = :site')
                ->setParameter('site', $data['site']);
        }
        if (!empty($data['etat'])) {
            $qb->andWhere("e.id = 5 OR e.id = 7");
        }
        if (!empty($data['dateMin'])&&!empty($data['dateMax'])) {
            $qb->andWhere('s.date BETWEEN :dateMin AND :dateMax')
                ->setParameter('dateMin', $data['dateMin'])
                ->setParameter('dateMax', $data['dateMax']);
        }
        $qb->orderBy('s.date', 'DESC');
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
