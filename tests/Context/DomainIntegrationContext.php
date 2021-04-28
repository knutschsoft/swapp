<?php
declare(strict_types=1);

namespace App\Tests\Context;

use App\Entity\RegisterUserRequest;
use App\Entity\SystemicQuestion;
use App\Entity\Tag;
use App\Entity\Team;
use App\Entity\User;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behatch\Context\RestContext;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Webmozart\Assert\Assert;
use Webmozart\Assert\Assert as Assertion;

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
        Assertion::notNull($serviceContainer);
        Assertion::isInstanceOf($serviceContainer, Container::class);
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
            $team->setUsers($users);
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
}
