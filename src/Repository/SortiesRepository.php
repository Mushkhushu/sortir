<?php

namespace App\Repository;

use App\Entity\Sorties;

use App\Entity\User;
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
    public function findByFilter(mixed $data, $userID): array
    {

        $qb = $this->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->join('s.participants', 'p');

        //filtre pour savoir les sorties ou on est inscrit
        if (!empty($data['participants']) && empty($data['non_participants'])) {

            $userID = User::class . ':id';
            $qb->andWhere('p.id IN (:participants)')
                ->setParameter('participants', $userID);
        }

        //filtre pour savoir les sorties ou on ne l'est pas inscrit
        if (!empty($data['non_participants']) && empty($data['participants'])) {
            $qb->andWhere('p.id NOT IN (:non_participants)')
                ->setParameter('non_participants', $userID);
        }
        //sorties que j'organise
        if (!empty($data['organizator'])){
            $qb->andWhere('s.organizator = :organizator');
            $qb->setParameter('organizator', $userID);
        }

        if ($date) {
            $qb->andWhere('s.date = :date')
                ->setParameter('date', $date);
        }

        if ($nom) {
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', "%{$nom}%");
        }
        //filtre pour savoir si la sortie est passée
        if (!empty($data['etat'])) {
            $qb->andWhere("s.id = 5");
        };
        $qb->andWhere("e.id != 7")
            //->andWhere("s.s.participants ")
            ->orderBy('s.date', 'DESC');

            //retourner le résultat
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
