<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    protected function baseQueryBuilder()
    {
        return $this->createQueryBuilder('c')
            ->addSelect('ci', 'p')
            ->join('c.cartItems', 'ci')
            ->join('ci.product', 'p');
    }

    public function findWithItems(int $id): ?Cart
    {
        return $this->baseQueryBuilder()
            ->where('c.id = :id')->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult();
    }

    public function findAllByUserWithItems(int $id): array
    {
        return $this->baseQueryBuilder()
            ->where('c.user = :id')->setParameter('id', $id)
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()->getResult();
    }

    //    /**
    //     * @return Cart[] Returns an array of Cart objects
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

    //    public function findOneBySomeField($value): ?Cart
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
