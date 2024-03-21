<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Status;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class StatusEventService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateStatus(): void
    {
        // Objet référentiel date du jour
        $utz = new \DateTimeZone('Europe/Paris');
        $now = new DateTime('now', $utz);


        // Sélection de tous les events disponibles en base de données
        $events = $this->entityManager->getRepository(Event::class)->findAll();

        // Lecture des évènements
        foreach ($events as $event) {
            // $startEvent contient la date de début de l'évènement retranché de sa durée
            $startEvent = $event->getStartingDateTime()->sub($event->getDuration());
            // $fullEventTime contient l'ensemble de la date de début + sa durée
            $fullEventTime = $event->getStartingDateTime();
            // $cloture contient la date de clôture de l'évènement
            $cloture = $event->getRegistrationDeadline();

            switch ($event->getStatus()->getId()) {
                case 2 :
                    // Si la date de clôture est dépassée, on le passe en 'Clôturé'
                    if ($now > $cloture) {
                        $event->setStatus($this->entityManager->getRepository(Status::class)->find(3));
                    }
                    break;
                case 3 :
                    // Si l'évènement a débuté et que la date de clôture est dépassée, on l'actualise en 'En cours'
                    if ($now >= $startEvent && $now > $cloture) {
                        $event->setStatus($this->entityManager->getRepository(Status::class)->find(4));
                    }
                    break;
                case 4 :
                    // Si l'évènement est terminé, on l'actualise en 'Passé'
                    if ($fullEventTime < $now) {
                        $event->setStatus($this->entityManager->getRepository(Status::class)->find(5));
                    }
                    break;
                case 5 || 6 :
                    // Si l'évènement est daté de plus d'un mois, on l'archive
                    $archiveDate = clone $now->modify('-1 month');
                    if ($archiveDate >= $startEvent) {
                        $event->setStatus($this->entityManager->getRepository(Status::class)->find(7));
                    }
                    break;
            }

            $this->entityManager->persist($event);

        }
        $this->entityManager->flush();
    }
}