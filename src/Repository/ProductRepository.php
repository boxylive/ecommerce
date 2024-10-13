<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function paginate(int $page = 1, int $byPage = 4, ?string $field = null, ?string $order = null): array
    {
        $page = $page <= 0 ? 1 : $page;

        $qb = $this->createQueryBuilder('p')
            ->setFirstResult(($page - 1) * $byPage)
            ->setMaxResults($byPage);

        $field = in_array($field, ['name', 'price']) ? $field : null;

        if (!is_null($field)) {
            $order = in_array($order, ['asc', 'desc']) ? $order : null;
            $qb->orderBy('p.'.$field, $order);
        }

        return $qb->getQuery()->getResult();
    }

    public function totalPages(int $byPage = 4): int
    {
        return ceil($this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult() / $byPage);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
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

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
