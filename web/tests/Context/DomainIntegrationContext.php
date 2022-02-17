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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\Container;
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

    public function __construct(KernelInterface $kernel)
    {
        $this->initRepositories($kernel);
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assert::notNull($serviceContainer);
        Assert::isInstanceOf($serviceContainer, Container::class);
        $this->em = $serviceContainer->get('doctrine.orm.entity_manager');
        $this->passwordEncoder = $serviceContainer->get(UserPasswordHasherInterface::class);
        $this->jwtManager = $serviceContainer->get(JWTTokenManagerInterface::class);
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
        //if ($url === '/api/walks/export') {
        //    $this->restContext->iAddHeaderEqualTo('content-type', 'text/csv');
        //    $this->restContext->iAddHeaderEqualTo('accept', 'text/csv');
        //    $this->restContext->iAddHeaderEqualTo('accept', 'application/ld+json');
        //
        //
        //} else {
        $this->restContext->iAddHeaderEqualTo('content-type', 'application/ld+json');
        $this->restContext->iAddHeaderEqualTo('accept', 'application/ld+json');
        //}

        $urlItems = \explode('/', $url);
        $newUrlItems = [];
        foreach ($urlItems as $urlItem) {
            $newUrlItems[] = $this->enrichText($urlItem);
        }
        $url = \implode('/', $newUrlItems);

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
            $request->startTime = isset($row['startTime']) ? new \DateTime($row['startTime']) : new \DateTime();
            $request->weather = $row['weather'] ?? 'Arschkalt';
            $request->holidays = isset($row['holidays']) ? (bool) $row['holidays'] : false;
            $request->conceptOfDay = $row['conceptOfDay'] ?? 'My daily concept.';
            $request->walkTeamMembers = $team->getUsers()->toArray();
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
     * @Given /^there are exactly (?P<code>\d+) wayPoints in database$/
     *
     * @param string $count
     */
    public function thereAreExactlyWayPointsInDatabase(string $count): void
    {
        Assert::same(\count($this->wayPointRepository->findAll()), (int) $count);
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
            $walk = $this->getWalkByName($row['name']);
            if (isset($row['startTime'])) {
                Assert::eq($walk->getStartTime(), new \DateTime($row['startTime']));
            }
            if (isset($row['endTime'])) {
                Assert::eq($walk->getEndTime(), new \DateTime($row['endTime']));
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
            if (isset($row['imageName']) && $row['imageName']) {
                Assert::eq(
                    $wayPoint->getImageName(),
                    $row['imageName'] === '<null>' ? null : $row['imageName'],
                    'Wrong value for imageName: It is %s instead of expected %s.'
                );
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
            if (isset($row['note'])) {
                Assert::eq($wayPoint->getNote(), $row['note']);
            }
            if (isset($row['oneOnOneInterview'])) {
                Assert::eq($wayPoint->getOneOnOneInterview(), $row['oneOnOneInterview']);
            }
            if (isset($row['isMeeting'])) {
                Assert::eq($wayPoint->getIsMeeting(), (bool) $row['isMeeting']);
            }
            if (isset($row['wayPointTags'])) {
                $expectedTags = $this->getTagsFromString($row['wayPointTags']);
                $wayPointTags = $wayPoint->getWayPointTags();
                foreach ($expectedTags as $expectedTag) {
                    Assert::inArray($expectedTag, $wayPointTags->toArray());
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
            $ageRanges = $this->getAgeRangesFromString($row['ageRanges'] ?? '');
            $team->setAgeRanges($ageRanges);
            $team->setLocationNames(isset($row['locationNames']) && $row['locationNames'] ? \explode(',', $row['locationNames']) : []);
            $team->updateClient($this->getClientByEmail($row['client']));

            $this->em->persist($team);
        }
        $this->em->flush();
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
}
