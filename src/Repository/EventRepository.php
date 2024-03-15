<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventSearch;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findBySearch(SearchData $searchData)
    {
        $events = $this->createQueryBuilder('e')
            ->orderBy('e.startingDateTime', 'DESC');

        if (!empty($searchData->keyword)) {
            $events = $events
                ->andWhere('e.name LIKE :keyword')
                ->setParameter('keyword', "%{$searchData->keyword}%");
        }

        if(!empty($searchData->campus)) {
            $events = $events
                ->join('e.campus', 'campus')
                ->andWhere('e.campus = :campus')
                ->addSelect('campus')
                ->setParameter('campus', $searchData->campus);
        }

        return $events
            ->getQuery()
            ->getResult();
    }




    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
