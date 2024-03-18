<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

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

    public function findByFilters(SearchData $searchData, User $user): array
    {
        /**
         * Récupération de toutes les informations par des jointures de tables
         * Tri anti-chronologique
         */
        $events = $this->createQueryBuilder('e')
            ->leftjoin('e.campus', 'campus')
            ->leftjoin('e.creator', 'creator')
            ->leftjoin('e.attendeesList', 'participant')
            ->orderBy('e.startingDateTime', 'DESC');

        // Affichage des évènements par mot-clé
        if (!empty($searchData->keyword)) { // Si l'information est donnée dans la recherche
            $events = $events
                ->andWhere('e.name LIKE :keyword')
                ->setParameter('keyword', "%{$searchData->keyword}%");
        }

        // Affichage des évnènements par campus
        if (!empty($searchData->campus)) { // Si l'information est donnée dans la recherche
            $events = $events
                ->andWhere('campus.id = :campus')
                ->setParameter('campus', $searchData->campus);
        }

        // Affichage des évènements dans une plage de dates - date début
        if (!empty($searchData->beginDateTime)) { // Si l'information est donnée dans la recherche
            $events = $events
                ->andWhere('e.startingDateTime >= :beginDateTime')
                ->setParameter('beginDateTime', $searchData->beginDateTime);
        }

        // Affichage des évènements dans une plage de dates - date fin
        if (!empty($searchData->endDateTime)) {
            $events = $events
                ->andWhere('e.startingDateTime <= :endDateTime')
                ->setParameter('endDateTime', $searchData->endDateTime);
        }

        // Affichage des évnènements dont l'utilisateur connecté est l'organisateur
        if (!empty($searchData->isOrganizer)) {
            $events = $events
                ->andWhere('e.creator = :isOrganizer')
                ->setParameter('isOrganizer', $user->getId());
        }

        // Affichage des évènements auxquels l'utilisateur connecté participe
        if (!empty($searchData->isParticipant) && empty($searchData->isNotParticipant)) { // Si l'information est donnée dans la recherche et que son inverse n'est pas cochéed
            $events = $events
                ->andWhere('participant.id = :isParticipant')
                ->setParameter('isParticipant', $user->getId());
        }

        /** Affichage des évènements vides de participants
         * OU ne comportant pas l'utilisateur connecté
         */
        if (!empty($searchData->isNotParticipant) && empty($searchData->isParticipant)) { // Si l'information est donnée dans la recherche et que son inverse n'est pas cochée
            $events = $events
                ->andWhere('participant.id NOT IN (:isNotParticipant) OR participant.id IS NULL')
                ->setParameter('isNotParticipant', $user->getId());
        }

        // Affichage des évnènements terminés
        if (!empty($searchData->passedDateTime)) {
            $events = $events
                ->andWhere('e.status = 5');
        }

        // Finalisation de la requête + renvoi
        return $events
            ->getQuery()
            ->getResult();
    }


}







