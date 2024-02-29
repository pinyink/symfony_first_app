<?php

namespace App\Repository;

use App\Entity\CrudDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CrudDetail>
 *
 * @method CrudDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method CrudDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method CrudDetail[]    findAll()
 * @method CrudDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrudDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CrudDetail::class);
    }

//    /**
//     * @return CrudDetail[] Returns an array of CrudDetail objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CrudDetail
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
