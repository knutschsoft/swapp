<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'sessions')]
#[ORM\Entity()]
class Session
{
    #[ORM\Id]
    #[ORM\Column(name: 'sess_id', type: 'string', length: 128, nullable: false)]
    protected string $sessId;

    #[ORM\Column(name: 'sess_data', type: 'blob', nullable: false)]
    protected mixed $sessData;

    #[ORM\Column(name: 'sess_time', type: 'integer', nullable: false, options: ['unsigned' => false])]
    protected int $sessTime;

    #[ORM\Column(name: 'sess_lifetime', type: 'integer', nullable: false)]
    protected int $sessLifetime;

    public function getSessId(): string
    {
        return $this->sessId;
    }

    public function setSessId(string $sessId): void
    {
        $this->sessId = $sessId;
    }

    /** @return mixed */
    public function getSessData(): mixed
    {
        return $this->sessData;
    }

    /** @param mixed $sessData */
    public function setSessData(mixed $sessData): void
    {
        $this->sessData = $sessData;
    }

    public function getSessTime(): int
    {
        return $this->sessTime;
    }

    public function setSessTime(int $sessTime): void
    {
        $this->sessTime = $sessTime;
    }

    public function getSessLifetime(): int
    {
        return $this->sessLifetime;
    }

    public function setSessLifetime(int $sessLifetime): void
    {
        $this->sessLifetime = $sessLifetime;
    }
}
