<?php

use App\Entity\User;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Webmozart\Assert\Assert;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext extends Behat\MinkExtension\Context\MinkContext
{
    use RepositoryTrait;

    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(KernelInterface $kernel)
    {
        $this->initRepositories($kernel);
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        $this->em = $serviceContainer->get('doctrine.orm.entity_manager');
        $this->passwordEncoder = $serviceContainer->get(UserPasswordEncoderInterface::class);
    }

    /**
     * @Given /^the following users exists:$/
     *
     * @param TableNode $table
     */
    public function theFollowingUsersExists(TableNode $table): void
    {
        foreach ($table as $key => $row) {
            $roles = $row['roles'] ?? 'ROLE_USER';
            $roles = \explode(' ', $roles);

            $email = $row['email'] ?? 'Brain@narf.de'.$key;
            $username = $row['username'] ?? $email;
            $password = $row['password'] ?? $username;

            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEnabled(isset($row['enabled']) ? (bool) $row['enabled'] : true);
            $user->setRoles($roles);

            $this->userRepository->save($user);
        }
        $this->em->flush();
    }

    /**
     * @When /^I click on text "([^"]*)"$/
     *
     * @param string $arg1
     *
     * @throws \Throwable
     */
    public function iClickOnText(string $arg1): void
    {
        $arg1 = $this->fixStepArgument($arg1);
        $this->iWaitForTextToAppear($arg1, 25);
        $element = $this->getSession()->getPage()->find('css', 'a:contains("'.$arg1.'")');
        if (\is_null($element)) {
            $element = $this->getSession()->getPage()->find('css', 'label:contains("'.$arg1.'")');
        }
        if (\is_null($element)) {
            $element = $this->getSession()->getPage()->find('css', 'i:contains("'.$arg1.'")');
        }
        if (\is_null($element)) {
            $element = $this->getSession()->getPage()->find('css', 'span:contains("'.$arg1.'")');
        }
        if (\is_null($element)) {
            $element = $this->getSession()->getPage()->find('css', 'button:contains("'.$arg1.'")');
        }
        if (\is_null($element)) {
            $element = $this->getNodeElement('[role="button"]:contains("'.$arg1.'")');
        }
        $element->click();
    }

    private function getNodeElement(string $locator, ?int $tries = 25): NodeElement
    {
        return $this->spin(
            function () use ($locator) {
                $element = $this->getSession()->getPage()->find('css', $locator);
                Assert::notNull(
                    $element,
                    \sprintf(
                        'locator "%s" not found on page %s',
                        $locator,
                        $this->getSession()->getCurrentUrl()
                    )
                );

                return $element;
            },
            $tries
        );
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived()
    {
        if (null === $this->response) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @Given /^I am authenticated as "([^"]*)"$/
     */
    public function iAmAuthenticatedAs($username): void
    {
        $this->visit('/login');
        $this->fillField('username', $username);
        $this->fillField('password', $username);
        $this->pressButton('Anmelden');
        $this->iWaitForTextToAppear(sprintf('Logout (%s)', $username));
    }

    /**
     * @When  I wait for :text to appear
     *
     * @Then  I should see :text appear
     *
     * @param string   $text
     * @param int|null $tries
     *
     * @throws \Throwable
     */
    public function iWaitForTextToAppear(string $text, ?int $tries = 25): void
    {
        $this->spin(
            function () use ($text): void {
                $this->assertPageContainsText($text);
            },
            $tries
        );
        $this->assertPageContainsText($text);
    }

    /**
     * @When  I wait for :text to disappear
     *
     * @Then  I should see :text disappear
     *
     * @param string   $text
     * @param int|null $tries
     *
     * @throws \Throwable
     */
    public function iWaitForTextToDisappear(string $text, ?int $tries = 25): void
    {
        $this->spin(
            function () use ($text): void {
                $this->assertSession()->pageTextNotContains($text);
            },
            $tries
        );
    }

    public function spin(\Closure $closure, ?int $tries = 25)
    {
        for ($i = 0; $i <= $tries; ++$i) {
            try {
                return $closure();
            } catch (\Throwable $e) {
                if ($i === $tries) {
                    throw $e;
                }
            }

            \usleep(100000); // 100 milliseconds
        }
    }

    /**
     * @Given /^I am on \'([^\']*)\'$/
     *
     * @param string $url
     */
    public function iAmOn(string $url): void
    {
        $this->visit($url);
    }
}
