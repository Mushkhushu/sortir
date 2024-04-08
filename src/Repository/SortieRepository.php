<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findByFilter(array $data, User $user): array
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->leftJoin('s.participants', 'p')
            ->leftJoin('s.organizator', 'o');

        if (!empty($data['participants']) && empty($data['non_participants'])) {
            $qb->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $user);
        }
        if (!empty($data['non_participants']) && empty($data['participants'])) {
            $qb->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user);
        }
        if (!empty($data['organizator'])) {
            $qb->andWhere('s.organizator = :organizator')
                ->setParameter('organizator', $user);
        }
        if (!empty($data['search'])) {
            $qb->andWhere('s.nom LIKE :search')
                ->setParameter('search', "%{$data['search']}%");
        }
        if (!empty($data['site'])) {
            $qb->andWhere('o.site = :site')
                ->setParameter('site', $data['site']);
        }
        // Ne jamais utiliser des ID en dure dans le code comment je sais à quel état font référence ces IDs ?
        // J'ai donc utilisé des constantes mise dans la classe etat pour recup les ids.
        if (!empty($data['etat'])) {
            $qb->andWhere("e.id = :etat1 OR e.id = :etat2")
                ->setParameter('etat1', Etat::ETAT_PASSEE)
                ->setParameter('etat2', Etat::ETAT_ARCHIVEE);
        }
        if (!empty($data['dateMin'])&&!empty($data['dateMax'])) {
            $qb->andWhere('s.date BETWEEN :dateMin AND :dateMax')
                ->setParameter('dateMin', $data['dateMin'])
                ->setParameter('dateMax', $data['dateMax']);
        }
        $qb->orderBy('s.date', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
