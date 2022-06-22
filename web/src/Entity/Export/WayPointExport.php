<?php
declare(strict_types=1);

namespace App\Entity\Export;

use Symfony\Component\Serializer\Annotation\SerializedName;

class WayPointExport
{
    public int $id;
    #[SerializedName('Ort')]
    public string $locationName;
    #[SerializedName('Ankunft')]
    public string $visitedAt;
    #[SerializedName('Rundenname')]
    public string $walkName;
    #[SerializedName('Teamname')]
    public string $teamName;
    #[SerializedName('Beobachtung')]
    public string $note;
    #[SerializedName('Einzelgespräch')]
    public string $oneOnOneInterview;
    #[SerializedName('Meeting?')]
    public bool $isMeeting;
    #[SerializedName('direkte Kontakte')]
    public ?int $contactsCount;
    #[SerializedName('Anzahl Personen vor Ort')]
    public int $peopleCount;
}
