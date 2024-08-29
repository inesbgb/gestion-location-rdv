<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Entity\Reservation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }
    public function findAllClients()
    {
        return $this->createQueryBuilder('r')
            ->select('DISTINCT r.prenom, r.nom, r.telephone, r.email')
            ->getQuery()
            ->getResult();
    }
    public function findReservedDatesForProduct(Produit $produit): array
{
    $qb = $this->createQueryBuilder('r')
        ->select('r.dateDebut', 'r.dateFin')
        ->where('r.produit = :produit')
        ->setParameter('produit', $produit);

    $result = $qb->getQuery()->getResult();

    $dates = [];
    foreach ($result as $reservation) {
        $current = clone $reservation['dateDebut'];
        while ($current <= $reservation['dateFin']) {
            $dates[] = $current->format('Y-m-d');
            $current->modify('+1 day');
        }
    }

    return array_unique($dates);
}
// Ajoutez la méthode countMonthlyReservations ici
public function countMonthlyReservations(): int
{
    $firstDayOfMonth = new \DateTime('first day of this month');
    $firstDayOfNextMonth = clone $firstDayOfMonth;
    $firstDayOfNextMonth->modify('+1 month');

    return $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->where('r.dateDebut >= :firstDay')
        ->andWhere('r.dateDebut < :lastDay')
        ->setParameter('firstDay', $firstDayOfMonth)
        ->setParameter('lastDay', $firstDayOfNextMonth)
        ->getQuery()
        ->getSingleScalarResult();
}

// La méthode getMonthlyRevenue que nous avons définie précédemment
public function getMonthlyRevenue(): float
{
    $firstDayOfMonth = new \DateTime('first day of this month');
    $firstDayOfNextMonth = clone $firstDayOfMonth;
    $firstDayOfNextMonth->modify('+1 month');

    return $this->createQueryBuilder('r')
        ->select('SUM(p.prix)')
        ->join('r.produit', 'p')
        ->where('r.dateDebut >= :firstDay')
        ->andWhere('r.dateDebut < :lastDay')
        ->setParameter('firstDay', $firstDayOfMonth)
        ->setParameter('lastDay', $firstDayOfNextMonth)
        ->getQuery()
        ->getSingleScalarResult() ?? 0;
}
public function getRevenueForPeriod(string $startDate, string $endDate): float
{
    $result = $this->createQueryBuilder('r')
        ->select('SUM(p.prix)')
        ->join('r.produit', 'p')
        ->where('r.dateDebut BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->getQuery()
        ->getSingleScalarResult();

    return $result ?? 0;
}

public function getReservationsCountForPeriod(string $startDate, string $endDate): int
{
    $result = $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->where('r.dateDebut BETWEEN :start AND :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->getQuery()
        ->getSingleScalarResult();

    return $result ?? 0;
}
//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
