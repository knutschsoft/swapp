<?php
declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\Model\Info;
use ApiPlatform\OpenApi\OpenApi;
use Webmozart\Assert\Assert;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private readonly OpenApiFactoryInterface $decorated)
    {
    }

    /**
     * @param array<mixed> $context
     *
     * @return OpenApi
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $info = new Model\Info('Swapp', 'v1', 'Description of streetworkapp API');
        Assert::isInstanceOf($info, Info::class);

        return $openApi->withInfo($info);
    }
}
