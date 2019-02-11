<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sessions")
 */
class Session
{

    /**
     * @ORM\Id()
     * @ORM\Column(name="sess_id",type="string", length=128, nullable=false)
     * @var string
     */
    protected $sessId;

    /**
     * @ORM\Column(name="sess_data",type="blob", nullable=false)
     * @var Resource
     */
    protected $sessData;

    /**
     * @ORM\Column(name="sess_time",type="integer", nullable=false, options={"unsigned": false})
     * @var int
     */
    protected $sessTime;

    /**
     * @ORM\Column(name="sess_lifetime",type="integer", nullable=false)
     * @var int
     */
    protected $sessLifetime;

    public function getSessId()
    {
        return $this->sessId;
    }

    public function setSessId($sessId)
    {
        $this->sessId = $sessId;
    }

    public function getSessData()
    {
        return $this->sessData;
    }

    public function setSessData($sessData)
    {
        $this->sessData = $sessData;
    }

    public function getSessTime()
    {
        return $this->sessTime;
    }

    public function setSessTime($sessTime)
    {
        $this->sessTime = $sessTime;
    }

    public function getSessLifetime()
    {
        return $this->sessLifetime;
    }

    public function setSessLifetime($sessLifetime)
    {
        $this->sessLifetime = $sessLifetime;
    }
}
