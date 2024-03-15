<?php

namespace App\Entity;

use DateTime;

class EventSearch
{

    /**
     * Selection du campus
     * @var string|null
     */
    private ?string $campus;

    /**
     * Mot clé dans la recherche
     * @var string|null
     */
    private ?string $keyword;

    /**
     * Date de début de l'évènement
     * @var DateTime|null
     */
    private ?DateTime $dateStart;

    /**
     * Date de fin de l'évènement
     * @var DateTime|null
     */
    private ?DateTime $dateEnd;

    /**
     * Nombre maximum d'inscrits
     * @var int|null
     */
    private ?int $maxAttendees;

    /**
     * Options de recherche
     * Checkboxes
     * @var array
     */
    private array $searchOptions;












    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): EventSearch
    {
        $this->keyword = $keyword;
        return $this;
    }

    public function getMaxAttendees(): ?int
    {
        return $this->maxAttendees;
    }

    public function setMaxAttendees(?int $maxAttendees): EventSearch
    {
        $this->maxAttendees = $maxAttendees;
        return $this;
    }


}