<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221019085642 extends AbstractMigration
{
    private $wayPoints = [];

    #[\Override]
    public function getDescription(): string
    {
        return '';
    }


    #[\Override]
    public function preUp(Schema $schema): void
    {
        $query = $this->connection->createQueryBuilder()
            ->select(
                'id, JSON_EXTRACT(ageGroups, "$[*].peopleCount.count") as peopleCountList'
            )
            ->from('way_point')
            ->getSQL();

        $this->wayPoints = $this->connection->executeQuery($query)->fetchAllAssociative();
    }


    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD isWithPeopleCount TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE team CHANGE isWithPeopleCount isWithPeopleCount TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk ADD isWithPeopleCount TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE walk CHANGE isWithPeopleCount isWithPeopleCount TINYINT(1) NOT NULL');
        $this->addSql('UPDATE walk w SET w.isWithPeopleCount = 1 WHERE w.isWithAgeRanges = 1');
        $this->addSql('UPDATE team t SET t.isWithPeopleCount = 1 WHERE t.isWithAgeRanges = 1');
        $this->addSql('ALTER TABLE way_point ADD peopleCount INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE way_point CHANGE peopleCount peopleCount INT NOT NULL');
    }

    #[\Override]
    public function postUp(Schema $schema): void
    {
        foreach ($this->wayPoints as $wayPoint) {
            $id = $wayPoint['id'];
            $peopleCountList = $wayPoint['peopleCountList'];
            if (\is_null($peopleCountList)) {
                continue;
            }
            $peopleCount = \array_sum(\json_decode((string) $peopleCountList, null, 512, JSON_THROW_ON_ERROR));

            $query = $this->connection->createQueryBuilder()
                ->update('way_point')
                ->set('way_point.peopleCount', $peopleCount)
                ->where(\sprintf("way_point.id = %s", $id))
                ->getSQL();

            $this->connection->executeQuery($query);
        }
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE way_point DROP peopleCount');
        $this->addSql('ALTER TABLE team DROP isWithPeopleCount');
        $this->addSql('ALTER TABLE walk DROP isWithPeopleCount');
    }
}
