<?php
declare(strict_types=1);

namespace App\Tests\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\RestContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\Email;
use Webmozart\Assert\Assert;

final class EmailContext implements Context
{
    use EmailTrait;
    use RepositoryTrait;

    protected RestContext $restContext;
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->initRepositories($this->kernel);
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();
        $this->restContext = $environment->getContext(RestContext::class);
    }

    /**
     * @Then an email should be sent to :recipient with subject:
     *
     * @param string       $recipient
     * @param PyStringNode $text
     */
    public function anEmailShouldBeSentToWithSubject(
        string $recipient,
        PyStringNode $text
    ): void {

        // there should a queued event on mail transport
        // and an unqueued event on null transport; unqueued because already sent

        /** @var Email[] $messages */
        $messages = $this->getMailerMessagesForAddress($recipient);

        // 2 events => 2 messages
        Assert::count(
            $messages,
            2,
            \sprintf('Found %d messages for %s', \count($messages), $recipient)
        );

        foreach ($messages as $message) {
            Assert::same($message->getTo()[0]->getAddress(), $recipient);
            Assert::same($message->getSubject(), $text->getRaw());
        }
    }

    /**
     * @Then an email should be sent to :recipient containing a link to :paramValue and subject:
     */
    public function anEmailShouldContainingLinkAndSubject(
        string $recipient,
        string $paramValue,
        PyStringNode $text
    ): void {
        $this->anEmailShouldBeSentToWithSubject($recipient, $text);
        $this->anEmailShouldBeSentToContainingALinkTo($recipient, $paramValue);
    }

    /**
     * @Given /^NO email should be sent to "([^"]*)"$/
     */
    public function noEmailShoudBeSentTo(string $recipient): void
    {
        /** @var Email[] $messages */
        $messages = $this->getMailerMessagesForAddress($recipient);

        // 2 events => 2 messages
        Assert::count($messages, 0);
    }

    /**
     * @Given /^an email should be sent to "([^"]*)" containing a link to "([^"]*)"$/
     */
    public function anEmailShouldBeSentToContainingALinkTo(string $recipient, string $paramValue): void
    {
        /** @var Email[] $messages */
        $messages = $this->getMailerMessagesForAddress($recipient);

        $url = $paramValue;

        foreach ($messages as $message) {
            $htmlBody = $message->getHtmlBody();
            Assert::contains(
                $htmlBody,
                $url,
                \sprintf('%s did not contain a link %s.', $htmlBody, $url)
            );
        }
    }
}
