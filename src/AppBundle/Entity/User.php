<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * This is for testing doctrine only, will be replaced with real user entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @package AppBundle\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTeamMembers")
     */
    private $walks;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Team", inversedBy="users")
     * @ORM\JoinTable(name="users_teams")
     */
    private $teams;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->walks = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    public function setTeams(ArrayCollection $teams): void
    {
        $this->teams = $teams;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s (%s)',
            $this->getUsername(),
            $this->getEmail()
        );
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getWalks(): ArrayCollection
    {
        return $this->walks;
    }

    public function setWalks(ArrayCollection $walks): void
    {
        $this->walks = $walks;
    }

    public function addTeam(Team $team): void
    {
        $this->teams[] = $team;
    }

    public function removeTeam(Team $team): void
    {
        $this->teams->removeElement($team);
    }

    public function setFacebookId(string $facebookId): self
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    public function getFacebookId(): string
    {
        return $this->facebook_id;
    }

    /**
     * Set facebookAccessToken
     *
     * @param string $facebookAccessToken
     *
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebookAccessToken
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Add walk
     *
     * @param Walk $walk
     *
     * @return User
     */
    public function addWalk(Walk $walk)
    {
        $this->walks[] = $walk;

        return $this;
    }

    /**
     * Remove walk
     *
     * @param Walk $walk
     */
    public function removeWalk(Walk $walk)
    {
        $this->walks->removeElement($walk);
    }
}
