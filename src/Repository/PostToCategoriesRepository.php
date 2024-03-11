<?php

namespace App\Repository;

use App\Entity\PostToCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostToCategories>
 *
 * @method PostToCategories|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostToCategories|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostToCategories[]    findAll()
 * @method PostToCategories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostToCategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostToCategories::class);
    }

//    /**
//     * @return PostToCategories[] Returns an array of PostToCategories objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PostToCategories
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
