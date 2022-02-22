<?php
declare(strict_types=1);

namespace App\Tests\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\RestContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mime\Address;
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

        Assert::count(
            $messages,
            1,
            \sprintf('Found %d messages for %s', \count($messages), $recipient)
        );

        foreach ($messages as $message) {
            Assert::same($message->getTo()[0]->getAddress(), $recipient);
            Assert::same($message->getSubject(), $text->getRaw());
        }
    }

    /**
     * @Then an email should be sent to :recipient with content:
     *
     * @param string       $recipient
     * @param PyStringNode $text
     */
    public function anEmailShouldBeSentToWithContent(
        string $recipient,
        PyStringNode $text
    ): void {
        /** @var Email[] $messages */
        $messages = $this->getMailerMessagesForAddress($recipient);

        Assert::count(
            $messages,
            1,
            \sprintf('Found %d messages for %s', \count($messages), $recipient)
        );

        foreach ($messages as $message) {
            $addresses = \array_merge($message->getTo(), $message->getCc());
            $addressStrings = \array_map(
                static function (Address $address) {
                    return $address->getAddress();
                },
                $addresses
            );

            Assert::inArray($recipient, $addressStrings);
            Assert::contains($message->getHtmlBody(), $text->getRaw());
        }
    }
}
