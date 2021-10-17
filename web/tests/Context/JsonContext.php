<?php
declare(strict_types=1);

namespace App\Tests\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behatch\Context\JsonContext as BehatchJsonContext;
use Symfony\Component\HttpKernel\KernelInterface;

final class JsonContext extends RawMinkContext
{
    use RepositoryTrait;

    private BehatchJsonContext $jsonContext;

    public function __construct(KernelInterface $kernel)
    {
        $this->initRepositories($kernel);
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();
        $this->jsonContext = $environment->getContext(BehatchJsonContext::class);
    }

    /**
     * Checks, that given enriched JSON nodes are equal to givens values
     *
     * @Then the enriched JSON nodes should be equal to:
     */
    public function theEnrichedJsonNodesShouldBeEqualTo(TableNode $nodes): void
    {
        foreach ($nodes->getRowsHash() as $node => $text) {
            $text = $this->enrichText($text);
            $this->jsonContext->theJsonNodeShouldBeEqualTo($node, $text);
        }
    }
}
