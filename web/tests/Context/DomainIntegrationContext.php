<?php
declare(strict_types=1);

namespace App\Tests\Context;

use App\Dto\Client\ClientCreateRequest;
use App\Dto\TagCreateRequest;
use App\Dto\User\UserCreateRequest;
use App\Dto\Walk\WalkCreateRequest;
use App\Entity\Client;
use App\Entity\SystemicQuestion;
use App\Entity\Tag;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Walk;
use App\Entity\WayPoint;
use App\Value\ConfirmationToken;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behatch\Context\RestContext;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Webmozart\Assert\Assert;

final class DomainIntegrationContext extends RawMinkContext
{
    use RepositoryTrait;

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordEncoder;
    private RestContext $restContext;
    private JWTTokenManagerInterface $jwtManager;
    private string $publicDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->initRepositories($kernel);
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assert::notNull($serviceContainer);
        Assert::isInstanceOf($serviceContainer, Container::class);
        $this->em = $serviceContainer->get('doctrine.orm.entity_manager');
        $this->passwordEncoder = $serviceContainer->get(UserPasswordHasherInterface::class);
        $this->jwtManager = $serviceContainer->get(JWTTokenManagerInterface::class);
        $this->publicDir = (string) $serviceContainer->getParameter('kernel.public_dir');
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();
        $this->restContext = $environment->getContext(RestContext::class);
    }

    /**
     * @Given /^I am authenticated against api as "([^"]*)"$/
     *
     * @param string $email
     *
     * @throws \Throwable
     */
    public function iAmAuthenticatedAgainstApiAs(string $email): void
    {
        $user = $this->getUserByEmail($email);
        $token = $this->jwtManager->create($user);
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer $token");
    }

    /**
     * @Given I send an api platform :method request to :url with parameters:
     *
     * @param string    $method
     * @param string    $url
     * @param TableNode $data
     *
     * @return void
     */
    public function iSendAnApiPlatformRequestToWithParameters(string $method, string $url, TableNode $data): void
    {
        $parameters = [];

        foreach ($data->getHash() as $row) {
            if (!isset($row['key']) || !isset($row['value'])) {
                throw new \Exception("You must provide a 'key' and 'value' column in your table node.");
            }

            if (!\is_string($row['value'])) {
                $parameters[$row['key']] = $row['value'];
                continue;
            }
            $parameters[$row['key']] = $this->enrichText($row['value']);
        }

        $body = new PyStringNode([\json_encode($parameters)], 0);
        if (!\str_starts_with($url, '/api/way_points/export') && !\str_starts_with($url, '/api/walks/export')) {
            $this->restContext->iAddHeaderEqualTo('content-type', 'application/ld+json');
            $this->restContext->iAddHeaderEqualTo('accept', 'application/ld+json');
        }

        $url = $this->enrichUrl($url);

        $this->restContext->iSendARequestToWithBody($method, $this->locatePath($this->enrichText($url)), $body);
    }

    /**
     * @Given there is a non empty confirmationToken for :username
     *
     * @param string $username
     */
    public function thereIsANonEmptyConfirmationTokenFor(string $username): void
    {
        $user = $this->getUserByEmail($username);
        $this->em->refresh($user);
        Assert::false($user->getConfirmationToken()->isEmpty(), 'ConfirmationToken is empty.');
    }

    /**
     * @Given there is an empty confirmationToken for :username
     *
     * @param string $username
     */
    public function thereIsAnEmptyConfirmationTokenFor(string $username): void
    {
        $user = $this->getUserByEmail($username);
        $this->em->refresh($user);
        Assert::true($user->getConfirmationToken()->isEmpty(), 'ConfirmationToken is not empty.');
    }

    /**
     * @Given /^the following clients exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingClientsExists(TableNode $table): void
    {
        foreach ($table as $key => $row) {
            $clientInitRequest = new ClientCreateRequest();
            $clientInitRequest->email = $row['email'] ?? 'Client@narf.de'.$key;
            $clientInitRequest->name = $row['name'] ?? $clientInitRequest->email;
            $clientInitRequest->description = $row['description'] ?? '';
            $client = Client::fromClientInitRequest($clientInitRequest);

            if (isset($row['users'])) {
                $users = $this->getUsersFromString($row['users']);
                foreach ($users as $user) {
                    $client->addUser($user);
                }
            }

            $this->em->persist($client);
        }
        $this->em->flush();
    }

    /**
     * @Given /^the following users exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingUsersExists(TableNode $table): void
    {
        foreach ($table as $key => $row) {
            Assert::keyExists($row, 'client');
            $registerUserRequest = new UserCreateRequest();
            $registerUserRequest->email = $row['email'] ?? 'Clari@narf.de'.$key;
            $registerUserRequest->username = $row['username'] ?? $registerUserRequest->email;
            $registerUserRequest->roles = [];
            $registerUserRequest->client = $this->getClientByEmail($row['client']);
            $user = User::fromUserCreateRequest($registerUserRequest, $this->passwordEncoder);
            $user->changePassword($row['password'] ?? $registerUserRequest->email, $this->passwordEncoder);

            $isEnabled = $row['isEnabled'] ?? true;
            if ($isEnabled) {
                $user->enable();
            } else {
                $user->disable();
            }
            $rolesString = $row['roles'] ?? '';
            $roles = \explode(',', $rolesString);
            foreach ($roles as $role) {
                $user->addRole($role);
            }

            if (isset($row['confirmationToken']) && $row['confirmationToken']) {
                $confirmationToken = ConfirmationToken::fromString($row['confirmationToken']);
                $user->setConfirmationToken($confirmationToken);
            }

            $this->em->persist($user);
        }
        $this->em->flush();
    }

    /**
     * @Given /^the following systemic questions exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingSystemicQuestionsExists(TableNode $table): void
    {
        foreach ($table as $row) {
            Assert::keyExists($row, 'question');
            Assert::keyExists($row, 'client');
            $this->em->persist(SystemicQuestion::fromString($row['question'], $this->getClientByEmail($row['client'])));
        }
        $this->em->flush();
    }

    /**
     * @Given /^the following tags exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingTagsExists(TableNode $table): void
    {
        foreach ($table as $row) {
            Assert::keyExists($row, 'name');
            Assert::keyExists($row, 'color');
            Assert::keyExists($row, 'client');
            $request = new TagCreateRequest();
            $request->name = $row['name'];
            $request->color = $row['color'];
            $request->client = $this->getClientByEmail($row['client']);
            $tag = Tag::fromTagCreateRequest($request);
            $this->em->persist($tag);
        }
        $this->em->flush();
    }

    /**
     * @Given /^the following walks exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingWalksExists(TableNode $table): void
    {
        foreach ($table as $row) {
            Assert::keyExists($row, 'name');
            Assert::keyExists($row, 'team');
            $team = $this->getTeamByName($row['team']);
            $systemicQuestion = SystemicQuestion::fromString(
                $row['systemicQuestion'] ?? 'How are you?',
                $team->getClient()
            );
            $request = new WalkCreateRequest();
            $request->team = $team;
            $request->name = $row['name'];
            $request->startTime = isset($row['startTime']) ? new \DateTime($this->enrichText($row['startTime'])) : new \DateTime();
            $request->weather = $row['weather'] ?? 'Arschkalt';
            $request->holidays = isset($row['holidays']) ? (bool) $row['holidays'] : false;
            $request->conceptOfDay = $row['conceptOfDay'] ?? 'My daily concept.';
            $request->walkTeamMembers = $team->getUsers()->toArray();
            $request->guestNames = $team->getGuestNames();
            $walk = Walk::fromWalkCreateRequest($request, $systemicQuestion);

            if (isset($row['endTime'])) {
                $walk->setEndTime(new \DateTime($row['endTime']));
            }
            if (isset($row['systemicAnswer'])) {
                $walk->setSystemicAnswer($row['systemicAnswer']);
            }
            if (isset($row['reflection'])) {
                $walk->setWalkReflection($row['reflection']);
            }
            if (isset($row['commitments'])) {
                $walk->setCommitments($row['commitments']);
            }
            if (isset($row['insights'])) {
                $walk->setInsights($row['insights']);
            }
            if (isset($row['ageRanges'])) {
                $walk->setAgeRanges($this->getAgeRangesFromString($row['ageRanges']));
            }
            if (isset($row['isUnfinished']) && '' !== $row['isUnfinished']) {
                $walk->setIsUnfinished((bool) $this->enrichText($row['isUnfinished']));
            }

            $this->em->persist($walk);
        }
        $this->em->flush();
    }

    /**
     * @Given /^the following way points exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingWayPointsExists(TableNode $table): void
    {
        foreach ($table as $row) {
            Assert::keyExists($row, 'walkName');
            Assert::keyExists($row, 'locationName');
            $walk = $this->getWalkByName($row['walkName']);
            $wayPoint = WayPoint::fromWalk($walk);
            $wayPoint->setLocationName($row['locationName']);
            $wayPoint->setNote($row['beobachtung'] ?? 'null');
            $wayPoint->setOneOnOneInterview($row['einzelgespraech'] ?? 'null');
            $wayPoint->setVisitedAt(isset($row['visitedAt']) ? new \DateTime($row['visitedAt']) : new \DateTime());
            if ($walk->isWithContactsCount()) {
                $wayPoint->setContactsCount($this->enrichText($row['contactsCount'] ?? 'int<7>'));
            }
            if (isset($row['userGroups']) && '' !== $row['userGroups']) {
                $wayPoint->setUserGroups($this->getUserGroupsFromString($row['userGroups']));
            }
            if (isset($row['ageGroups']) && '' !== $row['ageGroups']) {
                $wayPoint->setAgeGroups($this->getAgeGroupsFromString($row['ageGroups']));
            }
            if (isset($row['tags']) && '' !== $row['tags']) {
                $wayPoint->setWayPointTags(new ArrayCollection($this->getTagsFromString($row['tags'])));
            }

            $this->em->persist($wayPoint);
        }
        $this->em->flush();
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) walks in database$/
     *
     * @param string $count
     */
    public function thereAreExactlyWalksInDatabase(string $count): void
    {
        Assert::same(\count($this->walkRepository->getFindAllQuery()->getResult()), (int) $count);
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) clients in database$/
     *
     * @param string $count
     */
    public function thereAreExactlyClientsInDatabase(string $count): void
    {
        Assert::same(\count($this->clientRepository->findAll()), (int) $count);
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) users in database$/
     *
     * @param string $count
     */
    public function thereAreExactlyUsersInDatabase(string $count): void
    {
        Assert::same(\count($this->userRepository->findAll()), (int) $count);
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) teams in database$/
     *
     * @param string $count
     */
    public function thereAreExactlyTeamsInDatabase(string $count): void
    {
        Assert::same(\count($this->teamRepository->findAll()), (int) $count);
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) systemicQuestions in database$/
     *
     * @param string $count
     */
    public function thereAreExactlySystemicQuestionsInDatabase(string $count): void
    {
        Assert::same(\count($this->systemicQuestionRepository->findAll()), (int) $count);
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) wayPoints in database$/
     *
     * @param string $count
     */
    public function thereAreExactlyWayPointsInDatabase(string $count): void
    {
        Assert::same(\count($this->wayPointRepository->findAll()), (int) $count);
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) tags in database$/
     *
     * @param string $count
     */
    public function thereAreExactlyTagsInDatabase(string $count): void
    {
        Assert::same(\count($this->tagRepository->findAll()), (int) $count);
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) tagWayPoints in database$/
     *
     * @param string $expectedCount
     */
    public function thereAreExactlyTagWayPointsInDatabase(string $expectedCount): void
    {
        $count = 0;
        foreach ($this->tagRepository->findAll() as $tag) {
            $count += $tag->getWayPoints()->count();
        }
        Assert::same($count, (int) $expectedCount);
    }

    /**
     * @Given /^there are exactly (?P<code>\d+) userWalks in database$/
     *
     * @param string $expectedCount
     */
    public function thereAreExactlyUserWalksInDatabase(string $expectedCount): void
    {
        $count = 0;
        foreach ($this->userRepository->findAll() as $user) {
            $count += $user->getWalks()->count();
        }
        Assert::same($count, (int) $expectedCount);
    }

    /**
     * @Given /^I can find the following users in database:$/
     *
     * @param TableNode $table
     *
     * @return void
     *
     * @throws \Exception
     */
    public function iCanFindTheFollowingUsersInDatabase(TableNode $table): void
    {
        $this->em->clear();
        foreach ($table as $row) {
            $user = $this->getUserByEmail($row['email']);
            if (isset($row['isEnabled']) && '' !== $row['isEnabled']) {
                Assert::eq($user->isEnabled(), (bool) $row['isEnabled']);
            }
            if (isset($row['lastLoginAt']) && '' !== $row['lastLoginAt']) {
                $lastLoginAt = $row['lastLoginAt'];
                if ('<null>' === $lastLoginAt) {
                    Assert::null($user->getLastLoginAt());
                } else {
                    Assert::notNull($user->getLastLoginAt());
                    $expectedLastLoginAt = new Carbon($lastLoginAt);
                    $lastLoginAt = new Carbon($user->getLastLoginAt());
                    Assert::true(
                        $lastLoginAt->diffInSeconds($expectedLastLoginAt) < 5,
                        \sprintf('Expected lastLoginAt "%s" is not same as value "%s".', $expectedLastLoginAt, $lastLoginAt)
                    );
                }
            }
        }
    }

    /**
     * @Given /^I can find the following teams in database:$/
     *
     * @param TableNode $table
     *
     * @return void
     *
     * @throws \Exception
     */
    public function iCanFindTheFollowingTeamsInDatabase(TableNode $table): void
    {
        $this->em->clear();
        foreach ($table as $row) {
            Assert::keyExists($row, 'name');
            $team = $this->getTeamByName($row['name']);
            if (isset($row['isWithContactsCount']) && '' !== $row['isWithContactsCount']) {
                Assert::eq($team->isWithContactsCount(), (bool) $this->enrichText($row['isWithContactsCount']));
            }
            if (isset($row['isWithUserGroups']) && '' !== $row['isWithUserGroups']) {
                Assert::eq($team->isWithUserGroups(), (bool) $this->enrichText($row['isWithUserGroups']));
            }
            if (isset($row['client']) && '' !== $row['client']) {
                Assert::eq($team->getClient()->getId(), $this->getClientByEmail($row['client']));
            }
            if (isset($row['users'])) {
                $expectedUsers = $this->getUsersFromString($row['walkTeamMembers']);
                $users = $team->getUsers();
                foreach ($expectedUsers as $expectedUser) {
                    Assert::inArray($expectedUser, $users->toArray());
                }
                Assert::count(
                    $users,
                    \count($expectedUsers),
                    \sprintf(
                        'Wrong number of users in team "%s". Expected %d. Got %d.',
                        $team->getName(),
                        \count($expectedUsers),
                        \count($users)
                    )
                );
            }
            if (isset($row['locationNames'])) {
                $expectedLocationNames = $this->getLocationNamesFromString($row['locationNames']);
                $locationNames = $team->getLocationNames();
                foreach ($expectedLocationNames as $expectedLocationName) {
                    Assert::inArray($expectedLocationName, $locationNames);
                }
                Assert::count(
                    $locationNames,
                    \count($expectedLocationNames),
                    \sprintf(
                        'Wrong number of locationNames in team "%s". Expected %d. Got %d.',
                        $team->getName(),
                        \count($expectedLocationNames),
                        \count($locationNames)
                    )
                );
            }
            if (isset($row['ageRanges'])) {
                $expectedAgeRanges = $this->getAgeRangesFromString($row['ageRanges']);
                $teamAgeRanges = $team->getAgeRanges();
                $frontendLabels = [];
                foreach ($teamAgeRanges as $teamAgeRange) {
                    $frontendLabels[] = $teamAgeRange->getFrontendLabel();
                }
                foreach ($expectedAgeRanges as $expectedAgeRange) {
                    Assert::inArray($expectedAgeRange->getFrontendLabel(), $frontendLabels);
                }
                Assert::count(
                    $teamAgeRanges,
                    \count($expectedAgeRanges),
                    \sprintf(
                        'Wrong number of teamAgeRanges in team "%s". Expected %d. Got %d.',
                        $team->getName(),
                        \count($expectedAgeRanges),
                        \count($teamAgeRanges)
                    )
                );
            }
        }
    }

    /**
     * @Given /^I can not find the following walks in database:$/
     *
     * @param TableNode $table
     *
     * @return void
     *
     * @throws \Exception
     */
    public function iCanNotFindTheFollowingWalksInDatabase(TableNode $table): void
    {
        $this->em->clear();
        foreach ($table as $row) {
            try {
                $this->getWayPointByLocationName($row['name']);
                Assert::false(true, \sprintf('there should be no Walk in database for name "%s"', $row['name']));
            } catch (\InvalidArgumentException) {
                // expected
            }
        }
    }

    /**
     * @Given /^I can find the following walks in database:$/
     *
     * @param TableNode $table
     *
     * @return void
     *
     * @throws \Exception
     */
    public function iCanFindTheFollowingWalksInDatabase(TableNode $table): void
    {
        $this->em->clear();
        foreach ($table as $row) {
            $name = $row['name'];
            $walk = $this->getWalkByName($this->enrichText($name));
            if (isset($row['isWithContactsCount']) && '' !== $row['isWithContactsCount']) {
                Assert::eq($walk->isWithContactsCount(), (bool) $this->enrichText($row['isWithContactsCount']));
            }
            if (isset($row['isWithGuests']) && '' !== $row['isWithGuests']) {
                Assert::eq($walk->isWithGuests(), (bool) $this->enrichText($row['isWithGuests']));
            }
            if (isset($row['guestNames']) && '' !== $row['guestNames']) {
                Assert::eq($walk->getGuestNames(), (array) $this->enrichText($row['guestNames']));
            }
            if (isset($row['startTime'])) {
                $allowedDistanceInSeconds = 90;
                $expectedStartTime = new Carbon($this->enrichText($row['startTime']));
                $lowerExpectedStartTime = $expectedStartTime->clone()->subSeconds($allowedDistanceInSeconds);
                $higherExpectedStartTime = $expectedStartTime->clone()->addSeconds($allowedDistanceInSeconds);
                $startTime = new Carbon($walk->getStartTime());
                Assert::true(
                    $startTime->isBetween($lowerExpectedStartTime, $higherExpectedStartTime),
                    \sprintf(
                        "Distance is larger than %d seconds. lower: %s startTime: %s higher: %s",
                        $allowedDistanceInSeconds,
                        $lowerExpectedStartTime->format('H:i:s'),
                        $startTime->format('H:i:s'),
                        $higherExpectedStartTime->format('H:i:s')
                    )
                );
            }
            if (isset($row['endTime'])) {
                $allowedDistanceInSeconds = 90;
                $expectedEndTime = new Carbon($this->enrichText($row['endTime']));
                $lowerExpectedEndTime = $expectedEndTime->clone()->subSeconds($allowedDistanceInSeconds);
                $higherExpectedEndTime = $expectedEndTime->clone()->addSeconds($allowedDistanceInSeconds);
                $endTime = new Carbon($walk->getEndTime());
                Assert::true(
                    $endTime->isBetween($lowerExpectedEndTime, $higherExpectedEndTime),
                    \sprintf(
                        "Distance is larger than %d seconds. lower: %s endTime: %s higher: %s",
                        $allowedDistanceInSeconds,
                        $lowerExpectedEndTime->format('H:i:s'),
                        $endTime->format('H:i:s'),
                        $higherExpectedEndTime->format('H:i:s')
                    )
                );
            }
            if (isset($row['walkTeamMembers'])) {
                $expectedUsers = $this->getUsersFromString($row['walkTeamMembers']);
                $walkUsers = $walk->getWalkTeamMembers();
                foreach ($expectedUsers as $expectedUser) {
                    Assert::inArray($expectedUser, $walkUsers->toArray());
                }
                Assert::count(
                    $walkUsers,
                    \count($expectedUsers),
                    \sprintf(
                        'Wrong number of walkTeamMembers in walk "%s". Expected %d. Got %d.',
                        $walk->getName(),
                        \count($expectedUsers),
                        \count($walkUsers)
                    )
                );
            }
            if (isset($row['conceptOfDay'])) {
                Assert::same($walk->getConceptOfDay(), $this->enrichText($row['conceptOfDay']));
            }
            if (isset($row['commitments'])) {
                Assert::same($walk->getCommitments(), $this->enrichText($row['commitments']));
            }
            if (isset($row['insights'])) {
                Assert::same($walk->getCommitments(), $this->enrichText($row['insights']));
            }
            if (isset($row['systemicAnswer'])) {
                Assert::same($walk->getSystemicAnswer(), $this->enrichText($row['systemicAnswer']));
            }
            if (isset($row['walkReflection'])) {
                Assert::same($walk->getWalkReflection(), $this->enrichText($row['walkReflection']));
            }
        }
    }

    /**
     * @Given /^I can not find the following wayPoints in database:$/
     *
     * @param TableNode $table
     *
     * @return void
     *
     * @throws \Exception
     */
    public function iCanNotFindTheFollowingWayPointsInDatabase(TableNode $table): void
    {
        $this->em->clear();
        foreach ($table as $row) {
            try {
                $this->getWayPointByLocationName($row['locationName']);
                Assert::false(true, \sprintf('there should be no WayPoint in database for location name "%s"', $row['locationName']));
            } catch (\InvalidArgumentException) {
                // expected
            }
        }
    }

    /**
     * @Given /^I can find the following wayPoints in database:$/
     *
     * @param TableNode $table
     *
     * @return void
     *
     * @throws \Exception
     */
    public function iCanFindTheFollowingWayPointsInDatabase(TableNode $table): void
    {
        $this->em->clear();
        foreach ($table as $row) {
            $wayPoint = $this->getWayPointByLocationName($row['locationName']);
            if (isset($row['walk'])) {
                $walk = $this->getWalkByName($row['walk']);
                Assert::eq($wayPoint->getWalk()->getId(), $walk->getId());
            }
            if (isset($row['contactsCount']) && '' !== $row['contactsCount']) {
                Assert::eq($wayPoint->getContactsCount(), $this->enrichText($row['contactsCount']));
            }
            if (isset($row['imageName']) && '' !== $row['imageName']) {
                $imageName = $row['imageName'];
                if ('<null>' === $imageName) {
                    Assert::null($wayPoint->getImageName());
                } else {
                    Assert::notNull($wayPoint->getImageName());
                    $imageNameList = \explode('_', $imageName);
                    Assert::count($imageNameList, 2);
                    $timestamp = $this->enrichText($imageNameList[0]);
                    Assert::contains($wayPoint->getImageName(), $imageNameList[1]);
                    $expectedTimestamp = Carbon::createFromTimestamp($timestamp);
                    $actualTimestamp = Carbon::createFromTimestamp(\explode('_', $wayPoint->getImageName())[0]);
                    Assert::true(
                        $expectedTimestamp->diffInSeconds($actualTimestamp) < 5,
                        \sprintf(
                            'Expected timestamp in imageName "%s" is not same as value "%s". Diff is "%d seconds".',
                            $expectedTimestamp,
                            $actualTimestamp,
                            $expectedTimestamp->diffInSeconds($actualTimestamp)
                        )
                    );
                }
            }
            if (isset($row['ageGroups'])) {
                $expectedAgeGroups = $this->getAgeGroupsFromString($row['ageGroups']);
                $wayPointAgeGroups = $wayPoint->getAgeGroups();
                $frontendLabels = [];
                foreach ($wayPointAgeGroups as $wayPointAgeGroup) {
                    $frontendLabels[] = $wayPointAgeGroup->getFrontendLabel();
                }
                foreach ($expectedAgeGroups as $expectedAgeGroup) {
                    Assert::inArray($expectedAgeGroup->getFrontendLabel(), $frontendLabels);
                }
                Assert::count(
                    $wayPointAgeGroups,
                    \count($expectedAgeGroups),
                    \sprintf(
                        'Wrong number of wayPointAgeGroups in wayPoint "%s". Expected %d. Got %d.',
                        $wayPoint->getLocationName(),
                        \count($expectedAgeGroups),
                        \count($wayPointAgeGroups)
                    )
                );
            }
            if (isset($row['userGroups'])) {
                $expectedUserGroups = $this->getUserGroupsFromString($row['userGroups']);
                $wayPointUserGroups = $wayPoint->getUserGroups();
                $frontendLabels = [];
                foreach ($wayPointUserGroups as $wayPointUserGroup) {
                    $frontendLabels[] = $wayPointUserGroup->getFrontendLabel();
                }
                foreach ($expectedUserGroups as $expectedUserGroup) {
                    Assert::inArray($expectedUserGroup->getFrontendLabel(), $frontendLabels);
                }
                Assert::count(
                    $wayPointUserGroups,
                    \count($expectedUserGroups),
                    \sprintf(
                        'Wrong number of wayPointUserGroups in wayPoint "%s". Expected %d. Got %d.',
                        $wayPoint->getLocationName(),
                        \count($expectedUserGroups),
                        \count($wayPointUserGroups)
                    )
                );
            }
            if (isset($row['note'])) {
                Assert::eq($wayPoint->getNote(), $row['note']);
            }
            if (isset($row['oneOnOneInterview'])) {
                Assert::eq($wayPoint->getOneOnOneInterview(), $row['oneOnOneInterview']);
            }
            if (isset($row['isMeeting'])) {
                Assert::eq($wayPoint->getIsMeeting(), (bool) $row['isMeeting']);
            }
            if (isset($row['visitedAt'])) {
                $allowedDistanceInSeconds = 90;
                $expectedVisitedAt = new Carbon($this->enrichText($row['visitedAt']));
                $lowerExpectedVisitedAt = $expectedVisitedAt->clone()->subSeconds($allowedDistanceInSeconds);
                $higherExpectedVisitedAt = $expectedVisitedAt->clone()->addSeconds($allowedDistanceInSeconds);
                //$higherExpectedVisitedAt = $lowerExpectedVisitedAt;
                $visitedAt = new Carbon($wayPoint->getVisitedAt());
                Assert::true(
                    $visitedAt->isBetween($lowerExpectedVisitedAt, $higherExpectedVisitedAt),
                    \sprintf(
                        "Distance is larger than %d seconds. lower: %s visitedAt: %s higher: %s",
                        $allowedDistanceInSeconds,
                        $lowerExpectedVisitedAt->format('H:i:s'),
                        $visitedAt->format('H:i:s'),
                        $higherExpectedVisitedAt->format('H:i:s')
                    )
                );
            }
            if (isset($row['wayPointTags'])) {
                $expectedTags = $this->getTagsFromString($row['wayPointTags']);
                $wayPointTags = $wayPoint->getWayPointTags();
                foreach ($expectedTags as $expectedTag) {
                    Assert::inArray($expectedTag, $wayPointTags->toArray(), 'Expected "%s" to be in list of wayPointTags "%2$s" from database object.');
                }
                Assert::count(
                    $wayPointTags,
                    \count($expectedTags),
                    \sprintf(
                        'Wrong number of wayPointTags in wayPoint "%s". Expected %d. Got %d.',
                        $wayPoint->getLocationName(),
                        \count($expectedTags),
                        \count($wayPointTags)
                    )
                );
            }
        }
    }

    /**
     * @Given /^the following teams exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingTeamsExists(TableNode $table): void
    {
        foreach ($table as $key => $row) {
            Assert::keyExists($row, 'client');
            $team = new Team();
            $team->setName($row['name'] ?? 'Clari@narf.de'.$key);
            $users = $this->getUsersFromString($row['users'] ?? '');
            $team->setUsers(new ArrayCollection($users));
            $team->setLocationNames(isset($row['locationNames']) && $row['locationNames'] ? \explode(',', $row['locationNames']) : []);
            $isWithContactsCount = false;
            if (isset($row['isWithContactsCount']) && '' !== $row['isWithContactsCount']) {
                $isWithContactsCount = (bool) $this->enrichText($row['isWithContactsCount']);
            }
            $team->setIsWithContactsCount($isWithContactsCount);
            $isWithAgeRanges = true;
            if (isset($row['isWithAgeRanges']) && '' !== $row['isWithAgeRanges']) {
                $isWithAgeRanges = (bool) $this->enrichText($row['isWithAgeRanges']);
            }
            $team->setIsWithAgeRanges($isWithAgeRanges);
            if ($team->isWithAgeRanges()) {
                $ageRanges = $this->getAgeRangesFromString($row['ageRanges'] ?? '1-2,3-10');
                $team->setAgeRanges($ageRanges);
            }
            $isWithUserGroups = false;
            if (isset($row['isWithUserGroups']) && '' !== $row['isWithUserGroups']) {
                $isWithUserGroups = (bool) $this->enrichText($row['isWithUserGroups']);
            }
            if (isset($row['userGroupNames'])) {
                $team->setUserGroupNames($this->getUserGroupNamesFromString($row['userGroupNames']));
            }
            $team->setIsWithUserGroups($isWithUserGroups);
            $isWithGuests = false;
            if (isset($row['isWithGuests']) && '' !== $row['isWithGuests']) {
                $isWithGuests = (bool) $this->enrichText($row['isWithGuests']);
            }
            if ($isWithGuests && isset($row['guestNames'])) {
                $team->setGuestNames((array) $this->enrichText($row['guestNames']));
            }
            $team->setIsWithGuests($isWithGuests);
            $team->updateClient($this->getClientByEmail($row['client']));

            $this->em->persist($team);
        }
        $this->em->flush();
    }

    /**
     * @Then /^I can find the file "([^"]*)" in public folder$/
     *
     * @param string $file
     *
     * @example I can find the file "/images/way_points/timestamp<now>_AreYouDrunkMyDear.jpg" in public folder
     */
    public function iCanFindTheFileInPublicFolder(string $file): void
    {
        $isFound = $this->isFileInPublicDir($file);
        Assert::true($isFound, \sprintf('Did not find file "%s"', $file));
    }

    /**
     * @Then /^I can not find the file "([^"]*)" in public folder$/
     *
     * @param string $file
     *
     * @example I can not find the file "/images/way_points/timestamp<now>_AreYouDrunkMyDear.jpg" in public folder
     */
    public function iCanNotFindTheFileInPublicFolder(string $file): void
    {
        $isFound = $this->isFileInPublicDir($file);
        Assert::false($isFound, \sprintf('Did find file "%s"', $file));
    }

    /**
     * @When /^I enable user "([^"]*)"$/
     *
     * @param string $email
     */
    public function iEnableUser(string $email): void
    {
        $user = $this->getUserByEmail($email);
        $user->enable();
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @When /^I disable user "([^"]*)"$/
     *
     * @param string $email
     */
    public function iDisableUser(string $email): void
    {
        $user = $this->getUserByEmail($email);
        $user->disable();
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param string $teamsString
     *
     * @return string[]
     */
    private function getTeamIdsFromTeamsString(string $teamsString): array
    {
        $teamStringList = \explode(',', \trim($teamsString));
        $teamIds = [];
        foreach ($teamStringList as $teamString) {
            if (!$teamString) {
                continue;
            }
            $teamIds[] = (string) $this->getTeamByName($teamString)->getId();
        }

        return $teamIds;
    }

    /**
     * @param string $usersString
     *
     * @return string[]
     */
    private function getUserIdsFromUsernamesString(string $usersString): array
    {
        $userStringList = \explode(',', \trim($usersString));
        $userIds = [];
        foreach ($userStringList as $usernameString) {
            if (!$usernameString) {
                continue;
            }
            $userIds[] = (string) $this->getUserByEmail($usernameString)->getId();
        }

        return $userIds;
    }

    private function isFileInPublicDir(string $file): bool
    {
        $fileList = \explode('/', $file);
        Assert::minCount($fileList, 3);
        $filename = $fileList[\count($fileList) - 1];
        $filenameList = \explode('_', $filename);
        Assert::minCount($filenameList, 2);
        $expectedTime = Carbon::createFromTimestamp($this->enrichText($filenameList[0]));
        \array_pop($fileList);
        $dirName = \implode('/', $fileList);
        Assert::string($dirName);
        $finder = new Finder();
        $finder->in(\sprintf('%s%s', $this->publicDir, $dirName));
        foreach ($finder->files() as $finderFile) {
            $currentFileNameList = \explode('_', $finderFile->getFilename());
            if (\count($currentFileNameList) !== 2) {
                continue;
            }
            $time = Carbon::createFromTimestamp($currentFileNameList[0]);
            if ($expectedTime->diffInSeconds($time) < 3 && $currentFileNameList[1] === $filenameList[1]) {
                return true;
            }
        }

        return false;
    }
}
