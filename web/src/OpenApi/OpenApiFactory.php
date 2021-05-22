<?php
declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\OpenApi;
use Webmozart\Assert\Assert;

class OpenApiFactory implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
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
