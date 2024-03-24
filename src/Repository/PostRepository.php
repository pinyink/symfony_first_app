<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

//    /**
//     * @return Post[] Returns an array of Post objects
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

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function data($where = [], $params = [], $limit = 10, $offset = 0): array
    {
        $query = $this->createQueryBuilder('p');
        $query->select('p.id, p.url, p.title, p.summary, p.date, u.fullname, s.name as sampul_name, s.path as sampul_path');
        $query->leftJoin('p.user', 'u');
        $query->leftJoin('p.sampul', 's');
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $query->andWhere($value);
            }
        }
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $query->setParameter($key, $value);
            }
        }
        $query->orderBy('p.id', 'desc');
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }

    public function total() : int
    {
        $query = $this->createQueryBuilder('p')
            ->select('count(p.id) as total')
            ->getQuery()
            ->getOneOrNullResult();
        return $query['total'];
    }
}
