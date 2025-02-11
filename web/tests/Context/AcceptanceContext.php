<?php
declare(strict_types=1);

namespace App\Tests\Context;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverKeys;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class AcceptanceContext extends MinkContext
{
    use RepositoryTrait;

    private readonly RouterInterface $router;

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
        $this->iSetBrowserWindowSizeToX('2000', '1024');
        $this->iWaitForTextToAppear($username);
    }

    /**
     * Example: Then I go to swapp page "/"
     * Example: And I go to swapp page "/bats"
     * Example: And I go to swapp page "http://google.com"
     * Example: And I go to swapp page "/runde/walkId<Gorbitz>/wayPointId<Elbamare>"
     *
     * @Then /^(?:|I )go to swapp page "(?P<page>[^"]+)"$/
     */
    public function iGoToSwappPage(string $page): void
    {
        $this->visit($this->enrichUrl($page));
    }

    /**
     * @When  I wait for test element :locator to be not disabled
     *
     * @param string   $locator
     * @param int|null $tries
     *
     * @throws \Throwable
     */
    public function iWaitForTestElementToBeNotDisabled(string $locator, ?int $tries = 25): void
    {
        $element = $this->getTestElement($locator);
        $this->spin(
            static function () use ($element): void {
                Assert::false($element->hasAttribute('disabled'));
            },
            $tries
        );
        Assert::false($element->hasAttribute('disabled'));
    }

    /**
     * @When I submit Runde beginnen formular
     */
    public function iSubmitRundeBeginnenFormular(): void
    {
        $dataTestLocator = 'btn-Runde beginnen';
        $this->iWaitForTestElementToBeNotDisabled($dataTestLocator);
        $this->iClickOnTestElement($dataTestLocator);
        $this->iWaitForTestElementToDisappear($dataTestLocator);
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
    public function iWaitForTextToAppear(string $text, ?int $tries = 35): void
    {
        $text = $this->enrichText($text);
        $this->spin(
            function () use ($text): void {
                $this->assertPageContainsText($text);
            },
            $tries,
        );
        $this->assertPageContainsText($text);
    }

    /**
     * @When  I wait for test element :selector to appear
     *
     * @param string $selector
     *
     * @throws \Throwable
     */
    public function iWaitForTestElementToAppear(string $selector): void
    {
        $testElement = $this->getTestElement($selector);
        Assert::true($testElement->isVisible());
    }

    /**
     * @When  I wait for test element :selector to be selected
     *
     * @param string $selector
     *
     * @throws \Throwable
     */
    public function iWaitForTestElementToBeSelected(string $selector): void
    {
        $testElement = $this->getTestElement($selector);
        if ($testElement->hasClass('v-switch')) {
            $testElement = $testElement->find('css', 'input');
        }
        Assert::true($testElement->isSelected());
    }

    /**
     * @When  I wait for test element :selector to be not selected
     *
     * @param string $selector
     *
     * @throws \Throwable
     */
    public function iWaitForTestElementToBeNotSelected(string $selector): void
    {
        $testElement = $this->getTestElement($selector);
        if ($testElement->hasClass('v-switch')) {
            $testElement = $testElement->find('css', 'input');
        }
        Assert::false($testElement->isSelected());
    }

    /**
     * @When  I wait for element :selector to appear
     *
     * @param string $selector
     *
     * @throws \Throwable
     */
    public function iWaitForElementToAppear(string $selector): void
    {
        $testElement = $this->getNodeElement($selector);
        Assert::true($testElement->isVisible());
    }

    /**
     * @When  I wait for test element :selector to disappear
     *
     * @param string $selector
     *
     * @throws \Throwable
     */
    public function iWaitForTestElementToDisappear(string $selector): void
    {
        $this->spin(
            function () use ($selector): void {
                $tries = 10;
                try {
                    $testElement = $this->getTestElement($selector, $tries);
                } catch (\InvalidArgumentException) {
                    // all fine here
                    return;
                }
                Assert::false($testElement->isVisible());
            }
        );
    }

    /**
     * @Then (I )wait :count second(s)
     *
     * @param string $count
     */
    public function iWaitSeconds(string $count): void
    {
        \usleep((int) $count * 1000000);
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
     * @When /^I click on test element "([^"]*)"$/
     */
    public function iClickOnTestElement(string $dataTestSelector): void
    {
        $this->getTestElement($dataTestSelector)->click();
    }

    /**
     * @When /^I click on element with selector "([^"]*)"$/
     */
    public function iClickOnElementWithSelector(string $selector): void
    {
        $this->getNodeElement($selector)->click();
    }

    /**
     * @When /^I press key "([^"]*)" on element "([^"]*)"$/
     */
    public function iPressOnKey(string $key, string $dataTestSelector): void
    {
        $key = \str_replace(
            ['<down>', '<enter>'],
            [WebDriverKeys::ARROW_DOWN, WebDriverKeys::ENTER],
            $key
        );
        $xpath = $this->getTestElement($dataTestSelector)->getXpath();

        $this->getSession()->getDriver()->keyPress($xpath, $key);
    }

    /**
     * @When /^I click on aria label "([^"]*)"$/
     *
     * @param string $arg1
     *
     * @throws \Throwable
     */
    public function iClickOnAriaLabel(string $arg1): void
    {
        $locator = '[aria-label="'.$arg1.'"]';
        $element = $this->getSession()->getPage()->find('css', $locator);
        if (!$element) {
            Assert::false(true, \sprintf('Element with aria label selector "%s" could not be found.', $locator));
        }
        $element->click();
    }

    /**
     * @When /^I select time "([^"]*)" in time selector "([^"]*)"$/
     *
     * @param string $time             Format is H:mm.
     * @param string $dataTestSelector
     *
     * @throws \Throwable
     */
    public function iSelectTimeInTimeSelector(string $time, string $dataTestSelector): void
    {
        $explodedTime = \explode(':', $time);
        $hours = $explodedTime[0];
        $minutes = (int) $explodedTime[1];
        if ($minutes < 10) {
            $minutes .= '0' . $minutes;
        }
        $this->getTestElement($dataTestSelector)->click();
        $locatorHour = \sprintf('[data-test-id="hours-toggle-overlay-btn-0"]');
        $hourSelector = \sprintf('[data-test-id="%s"]', $hours);
        $locatorMinute = \sprintf('[data-test-id="minutes-toggle-overlay-btn-0"]');
        $minuteSelector = \sprintf('[data-test-id="%s"]', $minutes);
        $this->getNodeElement($locatorHour)->click();
        $this->getNodeElement($hourSelector)->click();
        $this->getNodeElement($locatorMinute)->click();
        $this->getNodeElement($minuteSelector)->click();
        $this->getNodeElement('body')->click();
    }

    /**
     * @When /^I select date "([^"]*)" in date selector "([^"]*)"$/
     *
     * @param string $date             Format is d.m.Y
     * @param string $dataTestSelector
     *
     * @throws \Throwable
     */
    public function iSelectDateInDateSelector(string $date, string $dataTestSelector): void
    {
        $rangePicker = $this->getTestElement($dataTestSelector);
        $rangePicker->mouseOver();
        $rangePicker->click();

        Carbon::setLocale('de');
        $date = Carbon::parse($date);
        $yearSelectElement = $this->getNodeElement("[data-dp-element='overlay-year']");
        $yearSelectElement->click();
        // following dataTestSelectors are inherently given by VueDatePicker
        $yearElement = $this->getNodeElement("[data-test-id='".$date->year."']");
        $yearElement->click();

        $monthSelectElement = $this->getNodeElement("[data-dp-element='overlay-month']");
        $monthSelectElement->click();
        $this->getNodeElement("[data-test-id='".$date->isoFormat('MMM')."']")->click();

        $dateFrom = $date->format('Y-m-d');
        $datePickerFrom = $this->getNodeElement(\sprintf("[data-test-id='dp-%s']", $dateFrom));
        $datePickerFrom->click();
    }

    /**
     * @When /^I wait for aria label "([^"]*)" to be active$/
     *
     * @param string $arg1
     *
     * @throws \Throwable
     */
    public function iWaitForAriaLabelToBeActive(string $arg1): void
    {
        $locator = '[aria-label="'.$arg1.'"]';
        $session = $this->getSession();
        $element = $session->getPage()->find('css', $locator);
        if (!$element) {
            Assert::false(true, \sprintf('Element with aria label selector "%s" could not be found.', $locator));
        }
        $this->spin(
            static function () use ($element): void {
                Assert::same($element->getAttribute('aria-disabled'), 'false');
            }
        );
        Assert::same($element->getAttribute('aria-disabled'), 'false');
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
            static function () use ($testElement, $dataTestSelector): void {
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
            static function () use ($testElement, $dataTestSelector): void {
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
            ['userId' => $user->getId(), 'confirmationToken' => $user->getConfirmationToken()->getToken()],
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
                \rtrim((string) $this->getMinkParameter('files_path'), \DIRECTORY_SEPARATOR),
                \DIRECTORY_SEPARATOR,
                \substr($value, 1)
            );
            if ($element->hasClass('v-file-input')) {
                $element->find('css', 'input')->attachFile($path);
            } else {
                $element->attachFile($path);
            }
        } else {
            $isVTextarea = $element->hasClass('v-textarea');
            $isVTextField = $element->hasClass('v-text-field');
            $isVSelect = $element->hasClass('v-select');
            $isDivField = $element->hasClass('v-combobox') || $isVTextarea || $isVTextField || $isVSelect;
            if ($isDivField) {
                $element->click();
                $element->keyPress(\str_repeat(WebDriverKeys::BACKSPACE, 15));
                $element->keyPress($this->enrichText($value));
                if (!$isVTextarea && !$isVTextField) {
                    $element->keyPress(WebDriverKeys::ENTER);
                }
                if ($isVSelect) {
                    $element->keyPress(WebDriverKeys::ENTER);
                }
                if (!$isVTextField) {
                    $this->getNodeElement('body')->click();
                }

                return;
            }
            $element->setValue($this->enrichText($value));
            $this->getNodeElement('body')->click();
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
