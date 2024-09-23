<?php

namespace App\Repository;

use DateTime;
use App\DTO\SearchData;
use App\Entity\Produit;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Produit::class);
        $this->paginator = $paginator;
    }
    public function isProduitDisponible(Produit $produit, ?\DateTimeInterface $dateDebut, ?\DateTimeInterface $dateFin): bool
    {
        // Si aucune date n'est fournie, on vérifie simplement le stock
        if (!$dateDebut || !$dateFin) {
            return $produit->isStock();
        }

        // Vérifier s'il y a des réservations qui chevauchent la période demandée
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(r.id)')
            ->leftJoin('p.reservations', 'r')
            ->where('p.id = :produitId')
            ->andWhere('r.dateDebut <= :dateFin')
            ->andWhere('r.dateFin >= :dateDebut')
            ->andWhere('r.retour = false')
            ->setParameter('produitId', $produit->getId())
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        $count = $qb->getQuery()->getSingleScalarResult();

        // Le produit est disponible s'il n'y a pas de réservations chevauchantes
        return $count == 0 && $produit->isStock();
    }
    public function findSearch(SearchData $searchData): PaginationInterface
{
    $query = $this->createQueryBuilder('p')
        ->leftJoin('p.categorie', 'c')
        ->leftJoin('p.taille', 't')
        ->leftJoin('p.reservations', 'r');

    if (!empty($searchData->query)) {
        $query = $query
            ->andWhere('p.designation LIKE :query')
            ->setParameter('query', "%{$searchData->query}%");
    }

    if ($searchData->categorie) {
        $query = $query
            ->andWhere('p.categorie = :categorie')
            ->setParameter('categorie', $searchData->categorie);
    }

    if ($searchData->taille) {
        $query = $query
            ->andWhere('p.taille = :taille')
            ->setParameter('taille', $searchData->taille);
    }

    if ($searchData->liquidation !== null) {
        $query = $query
            ->andWhere('p.liquidation = :liquidation')
            ->setParameter('liquidation', $searchData->liquidation);
    }

    if ($searchData->actif !== null) {
        $query = $query
            ->andWhere('p.actif = :actif')
            ->setParameter('actif', $searchData->actif);
    }

    if ($searchData->dateDebut instanceof DateTime && $searchData->dateFin instanceof DateTime) {
        $query->leftJoin('p.reservations', 'res', Join::WITH, 
            '(res.dateDebut <= :dateFin AND res.dateFin >= :dateDebut AND res.retour = false)')
            ->setParameter('dateDebut', $searchData->dateDebut->format('Y-m-d'))
            ->setParameter('dateFin', $searchData->dateFin->format('Y-m-d'));

        if ($searchData->stock !== null) {
            if ($searchData->stock) {
                // Robes disponibles pour la période
                $query->andWhere('res.id IS NULL AND p.stock = true');
            } else {
                // Robes indisponibles pour la période
                $query->andWhere('res.id IS NOT NULL OR p.stock = false');
            }
        }
    } elseif ($searchData->stock !== null) {
        // Si pas de période spécifiée, j'utilise juste le champ stock
        $query->andWhere('p.stock = :stock')
            ->setParameter('stock', $searchData->stock);
    }

    if ($searchData->stock !== null) {
        if ($searchData->dateDebut && $searchData->dateFin) {
            // Si une période est spécifiée, on considère la disponibilité pour cette période
            if ($searchData->stock) {
                $query = $query
                    ->andWhere('p.stock = true')
                    ->andWhere(
                        $query->expr()->orX(
                            $query->expr()->isNull('r.id'),
                            $query->expr()->lte('r.dateFin', ':dateDebut'),
                            $query->expr()->gte('r.dateDebut', ':dateFin')
                        )
                    );
            } else {
                $query = $query
                    ->andWhere(
                        $query->expr()->orX(
                            'p.stock = false',
                            $query->expr()->andX(
                                $query->expr()->lte('r.dateDebut', ':dateFin'),
                                $query->expr()->gte('r.dateFin', ':dateDebut')
                            )
                        )
                    );
            }
        } else {
            // Si aucune période n'est spécifiée, on considère la disponibilité actuelle
            $query = $query
                ->andWhere('p.stock = :stock')
                ->setParameter('stock', $searchData->stock);
        }
    }

    $query = $query->getQuery();

    return $this->paginator->paginate(
        $query,
        $searchData->page,
        10 // Nombre d'éléments par page
    );
    }
    public function getDisponibilites(array $produitIds, ?\DateTimeInterface $dateDebut, ?\DateTimeInterface $dateFin): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id', 'CASE WHEN COUNT(r.id) = 0 AND p.stock = true THEN true ELSE false END as isDisponible')
            ->leftJoin('p.reservations', 'r', Join::WITH, 
                'r.dateDebut <= :dateFin AND r.dateFin >= :dateDebut AND r.retour = false')
            ->where('p.id IN (:produitIds)')
            ->groupBy('p.id')
            ->setParameter('produitIds', $produitIds)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        $result = $qb->getQuery()->getResult();

        // Convertir le résultat en tableau associatif
        $disponibilites = [];
        foreach ($result as $row) {
            $disponibilites[$row['id']] = (bool) $row['isDisponible'];
        }

        return $disponibilites;
    }
//    /**
//     * @return Produit[] Returns an array of Produit objects
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

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
