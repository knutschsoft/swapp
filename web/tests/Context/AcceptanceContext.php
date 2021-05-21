<?php
declare(strict_types=1);

namespace App\Tests\Context;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class AcceptanceContext extends MinkContext
{
    use RepositoryTrait;

    private RouterInterface $router;

    public function __construct(KernelInterface $kernel)
    {
        $this->initRepositories($kernel);
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assert::notNull($serviceContainer);
        Assert::isInstanceOf($serviceContainer, Container::class);
        $this->router = $serviceContainer->get('router');
    }

    /**
     * @Given /^I am authenticated as "([^"]*)"$/
     *
     * @param string $username
     *
     * @throws \Throwable
     */
    public function iAmAuthenticatedAs(string $username): void
    {
        $this->visit('/anmeldung');
        $this->fillField('username', $username);
        $this->fillField('password', $username);

        $this->pressButton('Anmelden');
        $this->iWaitForTextToAppear($username);
    }

    /**
     * Example: Then I go to swapp page "/"
     * Example: And I go to swapp page "/bats"
     * Example: And I go to swapp page "http://google.com"
     * Example: And I go to swapp page "/runde/walk<Gorbitz>/wayPoint<Elbamare>"
     *
     * @Then /^(?:|I )go to swapp page "(?P<page>[^"]+)"$/
     */
    public function iGoToSwappPage(string $page): void
    {
        $parts = \explode('/', $page);
        foreach ($parts as $key => $part) {
            if (\str_starts_with($part, 'walk<') && \str_ends_with($part, '>')) {
                $parts[$key] = (string) $this->getWalkByName(\substr($part, 5, -1))->getId();
            } elseif (\str_starts_with($part, 'wayPoint<') && \str_ends_with($part, '>')) {
                $parts[$key] = (string) $this->getWayPointByLocationName(\substr($part, 9, -1))->getId();
            }
        }
        $page = \implode('/', $parts);
        $this->visit($page);
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
    public function iWaitForTextToAppear(string $text, ?int $tries = 50): void
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

    /**
     * @When /^I click on element "([^"]*)"$/
     */
    public function iClickOnElement(string $dataTestSelector): void
    {
        $this->getTestElement($dataTestSelector)->click();
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

    /**
     * @Then the element :dataTestSelector should be disabled
     */
    public function theElementShouldBeDisabled(string $dataTestSelector): void
    {
        $testElement = $this->getTestElement($dataTestSelector);
        $this->spin(
            function () use ($testElement, $dataTestSelector): void {
                Assert::true($testElement->hasAttribute('disabled'), \sprintf('The test element %s is not disabled.', $dataTestSelector));
            }
        );
        Assert::true($testElement->hasAttribute('disabled'), \sprintf('The test element %s is not disabled.', $dataTestSelector));
    }
    /**
     * @Then the element :dataTestSelector should be enabled
     */
    public function theElementShouldBeEnabled(string $dataTestSelector): void
    {
        $testElement = $this->getTestElement($dataTestSelector);
        $this->spin(
            function () use ($testElement, $dataTestSelector): void {
                Assert::false($testElement->hasAttribute('disabled'), \sprintf('The test element %s is not enabled.', $dataTestSelector));
            }
        );
        Assert::false($testElement->hasAttribute('disabled'), \sprintf('The test element %s is not enabled.', $dataTestSelector));
    }

    public function spin(\Closure $closure, ?int $tries = 25): ?NodeElement
    {
        for ($i = 0; $i <= $tries; $i++) {
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
     * @Given I am on page passwort-aendern for :username
     *
     * @param string $username
     */
    public function iAmOnPagePasswortAendernFor(string $username): void
    {
        $this->em->clear();
        $user = $this->getUserByEmail($username);
        $url = $this->router->generate(
            'user_password_reset',
            ['userId' => $user->getId()->toString(), 'requestPasswordResetToken' => $user->getRequestPasswordResetToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->visit($url);
    }

    /**
     * @Given /^I set browser window size to "([^"]*)" x "([^"]*)"$/
     */
    public function iSetBrowserWindowSizeToX(string $width, string $height): void
    {
        $this->getSession()->resizeWindow((int) $width, (int) $height, 'current');
    }

    /**
     * @When /^I enter "([^"]*)" in "([^"]*)" field$/
     */
    public function iEnterInField(string $value, string $dataTestLocator): void
    {
        $element = $this->getTestElement($dataTestLocator);
        if (\str_starts_with($value, '@')) {
            $path = \sprintf(
                "%s%s%s",
                \rtrim($this->getMinkParameter('files_path'), \DIRECTORY_SEPARATOR),
                \DIRECTORY_SEPARATOR,
                \substr($value, 1)
            );
            //$value = (new DataUriNormalizer())->normalize(new \SplFileInfo($path));
            $element->attachFile($path);
        } else {
            $element->setValue($value);
        }
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

    private function getTestElement(string $dataTestLocator, int $tries = 25): NodeElement
    {
        return $this->getNodeElement("[data-test='$dataTestLocator']", $tries);
    }
}