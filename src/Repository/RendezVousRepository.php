<?php

namespace App\Repository;

use App\Entity\Horaire;
use App\Entity\RendezVous;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function findUnavailableSlots(\DateTimeInterface $date): array
{
    $qb = $this->createQueryBuilder('r')
        ->select('r.heure_rdv')
        ->where('r.date_rdv = :date')
        ->setParameter('date', $date->format('Y-m-d'));

    $query = $qb->getQuery();
 

    $results = $query->getResult();
    
    // Convertir les résultats en un tableau de chaînes de créneaux horaires
    return array_map(function ($result) {
        return $result['heure_rdv']->format('H:i:s');
    }, $results);
}




public function findAvailableSlots(\DateTimeInterface $date): array
{
    // Récupérer tous les créneaux horaires possibles depuis la table Horaire
    $allSlots = $this->getEntityManager()
        ->getRepository(Horaire::class)
        ->findAll();

    

    // Convertir les objets Horaire en un tableau de chaînes de créneaux horaires
    $allSlotTimes = array_map(function ($horaire) {
        return $horaire->getSlot()->format('H:i:s');
    }, $allSlots);

 

    // Récupérer les créneaux horaires indisponibles pour la date donnée
    $unavailableSlots = $this->findUnavailableSlots($date);
    
    // Filtrer les créneaux horaires disponibles
    $availableSlots = array_filter($allSlotTimes, function ($slot) use ($unavailableSlots) {
        return !in_array($slot, $unavailableSlots);
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
