<?php
declare(strict_types=1);

namespace App\Entity\Fields;

use App\Value\UserGroupName;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait UserGroupNamesField
{
    /** @var UserGroupName[] */
    #[ORM\Column(type: 'json_document')]
    private array $userGroupNames;

    /** @return UserGroupName[] */
    #[Groups(['team:read', 'walk:read'])]
    public function getUserGroupNames(): array
    {
        return \array_values($this->userGroupNames);
    }

    /** @param UserGroupName[] $userGroupNames */
    public function setUserGroupNames(array $userGroupNames): void
    {
        $this->userGroupNames = $userGroupNames;
    }

    public function addUserGroup(UserGroupName $userGroupName): void
    {
        $this->userGroupNames[] = $userGroupName;
    }

    public function removeUserGroup(UserGroupName $userGroupName): void
    {
        foreach ($this->userGroupNames as $key => $userGroupToBeRemoved) {
            if ($userGroupName->equal($userGroupToBeRemoved)) {
                unset($this->userGroupNames[$key]);
            }
        }
    }
}
