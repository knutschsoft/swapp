<?php
declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Serializer\Annotation\Groups;

final readonly class UserGroup
{
    public function __construct(private UserGroupName $userGroupName, private PeopleCount $peopleCount)
    {
    }

    public static function fromUserGroupNameAndCount(UserGroupName $range, PeopleCount $peopleCount): self
    {
        return new self($range, $peopleCount);
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getUserGroupName(): UserGroupName
    {
        return $this->userGroupName;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getPeopleCount(): PeopleCount
    {
        return $this->peopleCount;
    }

    #[Groups(['walk:read', 'wayPoint:read'])]
    public function getFrontendLabel(): string
    {
        return \sprintf('%s', $this->userGroupName->getName());
    }

    public function equalType(self $userGroup): bool
    {
        return $this->userGroupName->equal($userGroup->getUserGroupName());
    }
}
