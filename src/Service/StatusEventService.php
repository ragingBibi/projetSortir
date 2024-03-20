<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Status;
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
        $now = new \DateTime('now');
        $archiveDate = $now->modify('-1 month');

        // Sélection de tous les events disponibles en db
        $events = $this->entityManager->getRepository(Event::class)->findAll();

        // Lecture des évènements
        foreach ($events as $event) {
            // Récupération des dates de chaque évènement
            $startEvent = $event->getStartingDateTime();
            $duration = $event->getDuration();
            $endEvent = $startEvent->add($duration);
            $cloture = $event->getRegistrationDeadline();


            // Mise à jour du statut de l'event  ouvert -> clôturé
            if ($now > $cloture && $event->getStatus()->getId(2)) {
                $event->setStatus($this->entityManager->getRepository(Status::class)->find(3));
            }

            // Mise à jour du statut de l'event clôturé -> en cours
            if ($now >= $startEvent && $now > $cloture && $event->getStatus()->getId(3)) {
                $event->setStatus($this->entityManager->getRepository(Status::class)->find(4));
            }

            // Mise à jour du statut de l'event en cours -> fini
            if ($now >= $startEvent && $event->getStatus()->getId(4)) {
                $event->setStatus($this->entityManager->getRepository(Status::class)->find(5));
            }

            // Mise à jour du statut de l'évènement fini/annulé (>=1mois) -> archivé
            if ($archiveDate >= $startEvent && $event->getStatus()->getId(5 | 6)) {
                $event->setStatus($this->entityManager->getRepository(Status::class)->find(7));

            }
            $this->entityManager->persist($event);

        }
        $this->entityManager->flush();
    }
}