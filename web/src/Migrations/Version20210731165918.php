<?php
declare(strict_types=1);

namespace Application\Migrations;

use App\Entity\Event;
use App\Entity\Serie;
use App\Entity\Team;
use App\Entity\Walk;
use App\Repository\SerieRepository;
use App\Repository\TeamRepository;
use App\Repository\WalkRepository;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

final class Version20210731165918 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var WalkRepository $walkRepository */
        $walkRepository = $em->getRepository(Walk::class);
        /** @var TeamRepository $teamRepository */
        $teamRepository = $em->getRepository(Team::class);
        $walks = $walkRepository->findAll();

        $count = 0;
        foreach ($walks as $walk) {
            $team = $teamRepository->findOneBy(['name' => $walk->getTeamName()]);
            if (!$team) {
                continue;
            }
            $walk->setWalkTeamMembers($team->getUsers());
            $count++;
            if ($count % 100 === 0) {
                $em->flush();
            }
        }

        $em->flush();
    }

    public function down(Schema $schema): void
    {
        $this->addSql('TRUNCATE TABLE user_walk;');
    }
}
