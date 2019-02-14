<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
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
     * @var Walk[]|Collection
     * @ORM\ManyToMany(targetEntity="Walk", inversedBy="walkTeamMembers")
     */
    private $walks;

    /**
     * @var Team[]|Collection
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
     * @return Team[]|Collection
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /**
     * @param Team[]|Collection $teams
     */
    public function setTeams($teams): void
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

    /**
     * @return Walk[]|Collection
     */
    public function getWalks(): Collection
    {
        return $this->walks;
    }

    /**
     * @param Walk[]|Collection $walks
     */
    public function setWalks($walks): void
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

    public function addWalk(Walk $walk): self
    {
        $this->walks[] = $walk;

        return $this;
    }

    public function removeWalk(Walk $walk): void
    {
        $this->walks->removeElement($walk);
    }
}
