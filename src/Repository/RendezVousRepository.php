<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    /**
     * Récupère les créneaux horaires indisponibles pour une date donnée.
     *
     * @param \DateTimeInterface $date La date pour laquelle vérifier les créneaux horaires indisponibles.
     * @return array Les créneaux horaires indisponibles.
     */
    public function findUnavailableSlots(\DateTimeInterface $dateTime): array
{
    return $this->createQueryBuilder('r')
        ->select('r.heure_rdv')
        ->where('r.date_rdv = :date')
        ->andWhere('r.statut = :statut')
        ->setParameter('date', $dateTime->format('Y-m-d'))
        ->setParameter('statut', true)
        ->getQuery()
        ->getResult();
}
    public function findAvailableSlots(\DateTimeInterface $date, array $allSlots): array
    {
      
        $unavailableSlots = $this->findUnavailableSlots($date);
    
        
        $unavailableTimes = array_map(function($slot) {
            return $slot['date_rdv'];
        }, $unavailableSlots);
    
      
        $availableSlots = array_filter($allSlots, function($slot) use ($unavailableTimes) {
            return !in_array($slot, $unavailableTimes);
        });
    
        return $availableSlots;
    }



    public function findTakenSlots(\DateTimeInterface $date): array
{
    $dateString = $date->format('Y-m-d');

    return $this->createQueryBuilder('r')
        ->select('r.heure_rdv')
        ->where('r.date_rdv = :date')
        ->andWhere('r.statut = :statut')
        ->setParameter('date', $dateString)
        ->setParameter('statut', true)
        ->getQuery()
        ->getResult();
}
    public function countTodayRdv(): int
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.date_rdv >= :today')
            ->andWhere('r.date_rdv < :tomorrow')
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findTodayRdv(): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0);
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');
    
        return $this->createQueryBuilder('r')
            ->andWhere('r.date_rdv >= :today')
            ->andWhere('r.date_rdv < :tomorrow')
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->orderBy('r.heure_rdv', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function countMonthlyRdv(): int
{
    $firstDayOfMonth = new \DateTime('first day of this month');
    $firstDayOfNextMonth = clone $firstDayOfMonth;
    $firstDayOfNextMonth->modify('+1 month');

    return $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->where('r.date_rdv >= :firstDay')
        ->andWhere('r.date_rdv < :lastDay')
        ->setParameter('firstDay', $firstDayOfMonth)
        ->setParameter('lastDay', $firstDayOfNextMonth)
        ->getQuery()
        ->getSingleScalarResult();
}
public function findByDate(\DateTime $date)
{
    return $this->createQueryBuilder('a')
        ->andWhere('DATE(a.dateRdv) = :date')
        ->setParameter('date', $date->format('Y-m-d'))
        ->orderBy('a.heureRdv', 'ASC')
        ->getQuery()
        ->getResult();
}
   

    //    /**
    //     * @return RendezVous[] Returns an array of RendezVous objects
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

    //    public function findOneBySomeField($value): ?RendezVous
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
