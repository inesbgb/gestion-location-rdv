<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }
    public function countNewClientsThisMonth(): int
    {
        $firstDayOfMonth = new \DateTime('first day of this month');
        $firstDayOfNextMonth = clone $firstDayOfMonth;
        $firstDayOfNextMonth->modify('+1 month');

        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.date_creation >= :firstDay')
            ->andWhere('c.date_creation < :lastDay')
            ->setParameter('firstDay', $firstDayOfMonth)
            ->setParameter('lastDay', $firstDayOfNextMonth)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Client[] Returns an array of Client objects
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

//    public function findOneBySomeField($value): ?Client
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
