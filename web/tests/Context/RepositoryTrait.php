<?php
declare(strict_types=1);

namespace App\Tests\Context;

use App\Entity\Client;
use App\Entity\Tag;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Walk;
use App\Entity\WayPoint;
use App\Repository\ClientRepository;
use App\Repository\SystemicQuestionRepository;
use App\Repository\TagRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\WalkRepository;
use App\Repository\WayPointRepository;
use App\Value\AgeGroup;
use App\Value\AgeRange;
use App\Value\ConfirmationToken;
use App\Value\Gender;
use App\Value\PeopleCount;
use App\Value\UserGroup;
use App\Value\UserGroupName;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Webmozart\Assert\Assert;

trait RepositoryTrait
{
    private EntityManagerInterface $em;
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private SystemicQuestionRepository $systemicQuestionRepository;
    private TagRepository $tagRepository;
    private TeamRepository $teamRepository;
    private WalkRepository $walkRepository;
    private WayPointRepository $wayPointRepository;

    public function initRepositories(KernelInterface $kernel): void
    {
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assert::notNull($serviceContainer);
        Assert::isInstanceOf($serviceContainer, Container::class);

        $clientRepository = $serviceContainer->get(ClientRepository::class);
        \assert($clientRepository instanceof ClientRepository);
        $this->clientRepository = $clientRepository;
        $userRepository = $serviceContainer->get(UserRepository::class);
        \assert($userRepository instanceof UserRepository);
        $this->userRepository = $userRepository;
        $systemicQuestionRepository = $serviceContainer->get(SystemicQuestionRepository::class);
        \assert($systemicQuestionRepository instanceof SystemicQuestionRepository);
        $this->systemicQuestionRepository = $systemicQuestionRepository;
        $teamRepository = $serviceContainer->get(TeamRepository::class);
        \assert($teamRepository instanceof TeamRepository);
        $this->teamRepository = $teamRepository;
        $walkRepository = $serviceContainer->get(WalkRepository::class);
        \assert($walkRepository instanceof WalkRepository);
        $this->walkRepository = $walkRepository;
        $wayPointRepository = $serviceContainer->get(WayPointRepository::class);
        \assert($wayPointRepository instanceof WayPointRepository);
        $this->wayPointRepository = $wayPointRepository;
        $tagRepository = $serviceContainer->get(TagRepository::class);
        \assert($tagRepository instanceof TagRepository);
        $this->tagRepository = $tagRepository;

        $em = $serviceContainer->get('doctrine.orm.entity_manager');
        \assert($em instanceof EntityManagerInterface);
        $this->em = $em;
    }

    protected function getClientByEmail(string $email): Client
    {
        return $this->clientRepository->findOneByEmail($email);
    }

    protected function getUserByEmail(string $email): User
    {
        $email = \trim($email);
        $user = $this->userRepository->findOneBy(['email' => $email]);
        Assert::notNull($user, \sprintf('User with email "%s" not found.', $email));

        return $user;
    }

    protected function getTagByName(string $name): Tag
    {
        $name = \trim($name);
        $tag = $this->tagRepository->findOneBy(['name' => $name]);
        Assert::notNull($tag, \sprintf('Tag with name "%s" not found.', $name));

        return $tag;
    }

    protected function getTeamByName(string $name): Team
    {
        $name = \trim($name);
        $team = $this->teamRepository->findOneBy(['name' => $name]);
        Assert::notNull($team, \sprintf('Team with name "%s" not found.', $name));

        return $team;
    }

    protected function getWalkByName(string $name): Walk
    {
        $name = \trim($name);
        $walk = $this->walkRepository->findOneBy(['name' => $name]);
        Assert::notNull($walk, \sprintf('Walk with name "%s" not found.', $name));

        return $walk;
    }

    protected function getWayPointByLocationName(string $locationName): WayPoint
    {
        $locationName = \trim($locationName);
        $wayPoint = $this->wayPointRepository->findOneBy(['locationName' => $locationName]);
        Assert::notNull($wayPoint, \sprintf('WayPoint with locationName "%s" not found.', $locationName));

        return $wayPoint;
    }

    /**
     * @param string $locationNamesString
     *
     * @return string[]
     */
    protected function getLocationNamesFromString(string $locationNamesString): array
    {
        $locationNames = [];
        if (!$locationNamesString) {
            return $locationNames;
        }
        $locationNameStrings = \explode(',', $locationNamesString);
        foreach ($locationNameStrings as $locationNameString) {
            $locationNames[] = $this->getUserByEmail($locationNameString);
        }

        return $locationNames;
    }

    /**
     * @param string $usersString
     *
     * @return User[]
     */
    protected function getUsersFromString(string $usersString): array
    {
        $users = [];
        if (!$usersString) {
            return $users;
        }
        $userStrings = \explode(',', $usersString);
        foreach ($userStrings as $userString) {
            $users[] = $this->getUserByEmail($userString);
        }

        return $users;
    }

    /**
     * @param string $tagsString
     *
     * @return Tag[]
     */
    protected function getTagsFromString(string $tagsString): array
    {
        $tags = [];
        if (!$tagsString) {
            return $tags;
        }
        $tagStrings = \explode(',', $tagsString);
        foreach ($tagStrings as $tagString) {
            $tags[] = $this->getTagByName($tagString);
        }

        return $tags;
    }

    /**
     * @param string $ageGroupsString
     *
     * @return AgeGroup[]
     */
    protected function getAgeGroupsFromString(string $ageGroupsString): array
    {
        $ageGroups = [];
        if (!$ageGroupsString) {
            return $ageGroups;
        }

        $ageGroupsStrings = \explode(';', $ageGroupsString);

        foreach ($ageGroupsStrings as $ageGroupString) {
            if ('' === $ageGroupString) {
                continue;
            }
            $parts = \explode(',', $ageGroupString);
            \assert(\count($parts) === 3, \sprintf('There are "%s" instead of 3 parts in string "%s".', \count($parts), $ageGroupString));
            $ageGroups[] = AgeGroup::fromRangeGenderAndCount(
                AgeRange::fromString($parts[0]),
                Gender::fromString($parts[1]),
                PeopleCount::fromInt((int) $parts[2]),
            );
        }

        return $ageGroups;
    }

    /**
     * @param string $userGroupsString
     *
     * @return UserGroup[]
     */
    protected function getUserGroupsFromString(string $userGroupsString): array
    {
        $userGroups = [];
        if (!$userGroupsString) {
            return $userGroups;
        }

        $userGroupsStrings = \explode(';', $userGroupsString);

        foreach ($userGroupsStrings as $userGroupString) {
            $parts = \explode(',', $userGroupString);
            \assert(\count($parts) === 2);
            $userGroups[] = UserGroup::fromUserGroupNameAndCount(
                UserGroupName::fromString($parts[0]),
                PeopleCount::fromInt((int) $parts[1]),
            );
        }

        return $userGroups;
    }

    /**
     * @param string $userGroupNamesString
     *
     * @return UserGroupName[]
     */
    protected function getUserGroupNamesFromString(string $userGroupNamesString): array
    {
        $userGroupNames = [];
        if (!$userGroupNamesString) {
            return $userGroupNames;
        }

        $userGroupNamesStrings = \explode(',', $userGroupNamesString);
        foreach ($userGroupNamesStrings as $userGroupNameString) {
            $userGroupNames[] = UserGroupName::fromString($userGroupNameString);
        }

        return $userGroupNames;
    }

    /**
     * @param string $ageRangesString
     *
     * @return AgeRange[]
     */
    protected function getAgeRangesFromString(string $ageRangesString): array
    {
        $ageRangeStrings = \explode(',', $ageRangesString);
        $ageRanges = [];
        if (!$ageRangesString) {
            return $ageRanges;
        }

        foreach ($ageRangeStrings as $ageRangeString) {
            $ageRanges[] = AgeRange::fromString($ageRangeString);
        }

        return $ageRanges;
    }

    protected function enrichUrl(string $url): string
    {
        $request = Request::create($url);

        $urlParts = \explode('/', $request->getPathInfo());
        \assert(\is_array($urlParts));
        $newUrlParts = [];
        foreach ($urlParts as $urlPart) {
            $newUrlParts[] = $this->enrichText($urlPart);
        }
        $url = \implode('/', $newUrlParts);

        if ($request->query->count()) {
            $url .= '?';
        }

        $append = '';
        foreach ($request->query->all() as $key => $value) {
            if (\is_array($value)) {
                foreach ($value as $innerKey => $valueEntry) {
                    $append .= $append ? '&' : '';
                    $append .= \sprintf('%s[%s]=%s', $key, $innerKey, \urlencode($this->enrichText($valueEntry)));
                }
                continue;
            }
            $append .= $append ? '&' : '';
            $append .= \sprintf('%s=%s', $key, \urlencode($this->enrichText($value)));
        }

        return \sprintf('%s%s', $url, $append);
    }

    private function enrichText(string $text): mixed
    {
        if ('<null>' === $text) {
            return null;
        }
        if ('<false>' === $text) {
            return false;
        }
        if ('<true>' === $text) {
            return true;
        }

        if (\str_starts_with($text, '@')) {
            $path = \sprintf(
                "%s%s%s",
                \rtrim($this->getMinkParameter('files_path'), \DIRECTORY_SEPARATOR),
                \DIRECTORY_SEPARATOR,
                \substr($text, 1)
            );

            return (new DataUriNormalizer())->normalize(new \SplFileInfo($path));
        }

        if (!\str_ends_with($text, '>')) {
            return $text;
        }
        $referenceIdentifikator = $this->getReferenceIdentifikator($text);

        if (\str_starts_with($text, 'timestamp<')) {
            return (string) (new Carbon($referenceIdentifikator))->timestamp;
        }
        if (\str_starts_with($text, 'string<')) {
            return \str_repeat('a', (int) $referenceIdentifikator);
        }
        if (\str_starts_with($text, 'int<')) {
            return (int) $referenceIdentifikator;
        }

        if (\str_starts_with($text, 'ageRanges<')) {
            $ageRanges = [];
            foreach ($this->getAgeRangesFromString($referenceIdentifikator) as $ageRange) {
                $ageRanges[] = [
                    'rangeStart' => $ageRange->getRangeStart(),
                    'rangeEnd' => $ageRange->getRangeEnd(),
                ];
            }

            return $ageRanges;
        }
        if (\str_starts_with($text, 'tagIri<')) {
            return \sprintf('/api/tags/%s', $this->gettagByName($referenceIdentifikator)->getId());
        }
        if (\str_starts_with($text, 'tagIris<')) {
            $tagIris = [];
            foreach ($this->getTagsFromString($referenceIdentifikator) as $tag) {
                $tagIris[] = \sprintf('/api/tags/%s', $tag->getId());
            }

            return $tagIris;
        }
        if (\str_starts_with($text, 'ageGroups<')) {
            $ageGroups = [];
            foreach ($this->getAgeGroupsFromString($referenceIdentifikator) as $ageGroup) {
                $ageGroups[] = [
                    'ageRange' => [
                        'rangeStart' => $ageGroup->getAgeRange()->getRangeStart(),
                        'rangeEnd' => $ageGroup->getAgeRange()->getRangeEnd(),
                    ],
                    'gender' => [
                        'gender' => $ageGroup->getGender()->getGender(),
                    ],
                    'peopleCount' => [
                        'count' => $ageGroup->getPeopleCount()->getCount(),
                    ],
                ];
            }

            return $ageGroups;
        }
        if (\str_starts_with($text, 'userGroups<')) {
            $userGroups = [];
            foreach ($this->getUserGroupsFromString($referenceIdentifikator) as $userGroup) {
                $userGroups[] = [
                    'userGroupName' => [
                        'name' => $userGroup->getUserGroupName()->getName(),
                    ],
                    'peopleCount' => [
                        'count' => $userGroup->getPeopleCount()->getCount(),
                    ],
                ];
            }

            return $userGroups;
        }
        if (\str_starts_with($text, 'userGroupNames<')) {
            $userGroupNames = [];
            foreach ($this->getUserGroupNamesFromString($referenceIdentifikator) as $userGroupName) {
                $userGroupNames[] = [
                    'name' => $userGroupName->getName(),
                ];
            }

            return $userGroupNames;
        }

        if (\str_starts_with($text, 'date<')) {
            $dateConfig = \explode(',', (string) $referenceIdentifikator);
            Assert::isArray($dateConfig);
            Assert::countBetween($dateConfig, 1, 2);
            Carbon::setlocale('de');

            return (new Carbon($dateConfig[0]))->translatedFormat($dateConfig[1] ?? 'd.m.Y');
        }

        if (\str_starts_with($text, 'teamIris<')) {
            $teams = [];
            foreach ($this->getTeamIdsFromTeamsString($referenceIdentifikator) as $teamId) {
                $teams[] = \sprintf('/api/teams/%s', $teamId);
            }

            return $teams;
        }

        if (\str_starts_with($text, 'teamIri<')) {
            return \sprintf('/api/teams/%s', $this->getTeamByName($referenceIdentifikator)->getId());
        }
        if (\str_starts_with($text, 'teamId<')) {
            return (string) $this->getTeamByName($referenceIdentifikator)->getId();
        }

        if (\str_starts_with($text, 'wayPointIri<')) {
            return \sprintf('/api/way_points/%s', (string) $this->getWayPointByLocationName($referenceIdentifikator)->getId());
        }
        if (\str_starts_with($text, 'wayPointId<')) {
            return (string) $this->getWayPointByLocationName($referenceIdentifikator)->getId();
        }

        if (\str_starts_with($text, 'walkIri<')) {
            return \sprintf('/api/walks/%s', $this->getWalkByName($referenceIdentifikator)->getId());
        }
        if (\str_starts_with($text, 'walkId<')) {
            return (string) $this->getWalkByName($referenceIdentifikator)->getId();
        }

        if (\str_starts_with($text, 'userIris<')) {
            $userIds = [];
            foreach ($this->getUserIdsFromUsernamesString($referenceIdentifikator) as $userId) {
                $userIds[] = \sprintf('/api/users/%s', $userId);
            }

            return $userIds;
        }

        if (\str_starts_with($text, 'userIri<')) {
            return \sprintf('/api/users/%s', (string) $this->getUserByEmail($referenceIdentifikator)->getId());
        }
        if (\str_starts_with($text, 'userId<')) {
            return (string) $this->getUserByEmail($referenceIdentifikator)->getId();
        }

        if (\str_starts_with($text, 'clientIri<')) {
            return \sprintf('/api/clients/%s', (string) $this->getClientByEmail($referenceIdentifikator)->getId());
        }
        if (\str_starts_with($text, 'clientId<')) {
            return (string) $this->getClientByEmail($referenceIdentifikator)->getId();
        }

        if (\str_starts_with($text, 'confirmationToken<')) {
            return [
                'token' => ConfirmationToken::fromString($referenceIdentifikator)->getToken(),
            ];
        }

        if (\str_starts_with($text, 'array<')) {
            return '' !== $referenceIdentifikator ? \explode(',', (string) $referenceIdentifikator) : [];
        }

        return \trim($text);
    }

    private function getReferenceIdentifikator(string $reference): string
    {
        Assert::endsWith($reference, '>');
        Assert::same(\substr_count($reference, '>'), 1, \sprintf('$reference "%s" should only contain one > sign.', $reference));
        Assert::same(\substr_count($reference, '<'), 1, \sprintf('$reference "%s" should only contain one < sign.', $reference));

        return \substr($reference, \strpos($reference, "<") + 1, -1);
    }
}
