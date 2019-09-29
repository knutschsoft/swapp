<?php
declare(strict_types=1);

namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ApplicationAvailabilityTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url): void
    {
        $this->loadAllFixtures();
        $credentials = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $client = static::makeClient($credentials);
        $client->request('GET', $url);
        $this->isSuccessful($client->getResponse());
    }

    public function urlProvider()
    {
        return [
            ['/walks'],
            ['/tag'],
            ['/createtag'],
            ['/eadmin/?action=list&entity=Team'],
            ['/eadmin/?action=list&entity=Walk'],
            ['/eadmin/?action=list&entity=WayPoint'],
            ['/eadmin/?action=list&entity=Tag'],
            ['/eadmin/?action=list&entity=User'],
            ['/eadmin/?action=list&entity=Guest'],
            ['/eadmin/?action=list&entity=SystemicQuestion'],
            // ...
        ];
    }

    /**
     * @return \Doctrine\Common\DataFixtures\Executor\AbstractExecutor|void|null
     */
    private function loadAllFixtures()
    {
        $this->loadFixtureFiles(
            [
                '@AppBundle/DataFixtures/ORM/test/tag.yml',
                '@AppBundle/DataFixtures/ORM/test/guest.yml',
                '@AppBundle/DataFixtures/ORM/test/team.yml',
                '@AppBundle/DataFixtures/ORM/test/user.yml',
                '@AppBundle/DataFixtures/ORM/test/walk.yml',
                '@AppBundle/DataFixtures/ORM/test/systemicQuestion.yml',
                '@AppBundle/DataFixtures/ORM/test/wayPoint.yml',
            ]
        );
    }
}
