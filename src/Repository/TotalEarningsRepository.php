<?php

namespace App\Repository;

use App\Entity\TotalEarnings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TotalEarnings>
 *
 * @method TotalEarnings|null find($id, $lockMode = null, $lockVersion = null)
 * @method TotalEarnings|null findOneBy(array $criteria, array $orderBy = null)
 * @method TotalEarnings[]    findAll()
 * @method TotalEarnings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TotalEarningsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TotalEarnings::class);
    }

    public function add(TotalEarnings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TotalEarnings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TotalEarnings[] Returns an array of TotalEarnings objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TotalEarnings
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
