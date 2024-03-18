<?php

namespace App\Model;

use App\Entity\Campus;
use DateTime;

class SearchData
{

    public mixed $keyword = '';

    public mixed $campus;

    public DateTime|null $beginDateTime;

    public DateTime|null $endDateTime;

    public mixed $passedDateTime;

    public mixed $isOrganizer;

    public bool $isParticipant;

    public bool $isNotParticipant;



}