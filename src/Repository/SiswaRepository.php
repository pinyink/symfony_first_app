<?php

namespace App\Repository;

use App\Entity\Siswa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Siswa>
 *
 * @method Siswa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Siswa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Siswa[]    findAll()
 * @method Siswa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiswaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Siswa::class);
    }

//    /**
//     * @return Siswa[] Returns an array of Siswa objects
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

//    public function findOneBySomeField($value): ?Siswa
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
