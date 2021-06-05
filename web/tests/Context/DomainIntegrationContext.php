<?php
declare(strict_types=1);

namespace App\Tests\Context;

use App\Entity\RegisterUserRequest;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Webmozart\Assert\Assert;

final class DomainIntegrationContext extends RawMinkContext
{
    use RepositoryTrait;

    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $passwordEncoder;
    private RestContext $restContext;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(KernelInterface $kernel)
    {
        $this->initRepositories($kernel);
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assert::notNull($serviceContainer);
        Assert::isInstanceOf($serviceContainer, Container::class);
        $this->em = $serviceContainer->get('doctrine.orm.entity_manager');
        $this->passwordEncoder = $serviceContainer->get(UserPasswordEncoderInterface::class);
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
            if ($row['value'] === '<null>') {
                $parameters[$row['key']] = null;
                continue;
            }

            $firstChar = \substr($row['value'], 0, 1);
            $lastChar = \substr($row['value'], -1, 1);
            if ('@' === $firstChar) {
                $path = \sprintf(
                    "%s%s%s",
                    \rtrim($this->getMinkParameter('files_path'), \DIRECTORY_SEPARATOR),
                    \DIRECTORY_SEPARATOR,
                    \substr($row['value'], 1)
                );
                $parameters[$row['key']] = (new DataUriNormalizer())->normalize(new \SplFileInfo($path));
            } elseif (\str_starts_with($row['value'], 'ageRanges<') && \str_ends_with($lastChar, '>')) {
                $value = \substr($row['value'], 10, -1);
                $parameters[$row['key']] = [];
                foreach ($this->getAgeRangesFromString($value) as $ageRange) {
                    $parameters[$row['key']][] = [
                        'rangeStart' => $ageRange->getRangeStart(),
                        'rangeEnd' => $ageRange->getRangeEnd(),
                    ];
                }
            } elseif (\str_starts_with($row['value'], 'teamIris<') && \str_ends_with($lastChar, '>')) {
                $value = \substr($row['value'], 9, -1);
                $parameters[$row['key']] = [];
                foreach ($this->getTeamIdsFromTeamsString($value) as $teamId) {
                    $parameters[$row['key']][] = \sprintf('/api/teams/%s', $teamId);
                }
            } elseif (\str_starts_with($row['value'], 'teamIri<') && \str_ends_with($lastChar, '>')) {
                $value = \substr($row['value'], 8, -1);
                $parameters[$row['key']] = \sprintf('/api/teams/%s', (string) $this->getTeamByName($value)->getId());
            } elseif (\str_starts_with($row['value'], 'userIris<') && \str_ends_with($lastChar, '>')) {
                $value = \substr($row['value'], 9, -1);
                $parameters[$row['key']] = [];
                foreach ($this->getUserIdsFromUsernamesString($value) as $userId) {
                    $parameters[$row['key']][] = \sprintf('/api/users/%s', $userId);
                }
            } elseif (\str_starts_with($row['value'], 'userIri<') && \str_ends_with($lastChar, '>')) {
                $value = \substr($row['value'], 8, -1);
                $parameters[$row['key']] = \sprintf('/api/users/%s', (string) $this->getUserByEmail($value)->getId());
            } elseif (\str_starts_with($row['value'], 'confirmationToken<') && \str_ends_with($lastChar, '>')) {
                $value = \substr($row['value'], 18, -1);
                $parameters[$row['key']] = [
                    'token' => ConfirmationToken::fromString($value)->getToken(),
                ];
            } else {
                $parameters[$row['key']] = \trim($row['value']);
            }
        }

        $body = new PyStringNode([\json_encode($parameters)], 0);
        $this->restContext->iAddHeaderEqualTo('content-type', 'application/ld+json');
        $this->restContext->iAddHeaderEqualTo('accept', 'application/ld+json');

        $this->restContext->iSendARequestToWithBody($method, $this->locatePath($url), $body);
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
     * @Given /^the following users exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingUsersExists(TableNode $table): void
    {
        foreach ($table as $key => $row) {
            $registerUserRequest = new RegisterUserRequest();
            $registerUserRequest->email = $row['email'] ?? 'Clari@narf.de'.$key;
            $registerUserRequest->password = $row['password'] ?? $registerUserRequest->email;
            $registerUserRequest->username = $row['username'] ?? $registerUserRequest->email;
            $user = User::fromRegisterUserRequest($registerUserRequest, $this->passwordEncoder);

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
            $this->em->persist(SystemicQuestion::fromString($row['question']));
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
            $tag = new Tag();
            $tag->setName($row['name']);
            $tag->setColor($row['color']);
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
            $systemicQuestion = SystemicQuestion::fromString($row['systemicQuestion'] ?? 'How are you?');
            $tag = Walk::prologue($team, $systemicQuestion);
            $tag->setName($row['name']);

            $this->em->persist($tag);
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
     * @Given /^the following teams exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingTeamsExists(TableNode $table): void
    {
        foreach ($table as $key => $row) {
            $team = new Team();
            $team->setName($row['name'] ?? 'Clari@narf.de'.$key);
            $users = $this->getUsersFromString($row['users'] ?? '');
            $team->setUsers(new ArrayCollection($users));
            $ageRanges = $this->getAgeRangesFromString($row['ageRanges'] ?? '');
            $team->setAgeRanges($ageRanges);

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
