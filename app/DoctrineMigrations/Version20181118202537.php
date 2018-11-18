<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181118202537 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE walk ADD ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
    }

    public function postUp(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $value = '{"6": {"#type": "AppBundle\\\\Value\\\\AgeRange", "rangeEnd": 5, "rangeStart": 0}, "7": {"#type": "AppBundle\\\\Value\\\\AgeRange", "rangeEnd": 11, "rangeStart": 6}, "8": {"#type": "AppBundle\\\\Value\\\\AgeRange", "rangeEnd": 17, "rangeStart": 12}, "9": {"#type": "AppBundle\\\\Value\\\\AgeRange", "rangeEnd": 21, "rangeStart": 18}, "10": {"#type": "AppBundle\\\\Value\\\\AgeRange", "rangeEnd": 27, "rangeStart": 22}}';

        $query = $this->connection->createQueryBuilder()
            ->update('team')
            ->set('team.ageRanges', json_encode($value))
            ->getSQL();

        $this->connection->executeQuery(
            $query
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE walk DROP ageRanges');
    }
}
