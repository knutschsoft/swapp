<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240613105329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('CREATE INDEX idx_walk_name_teamName ON walk (name, teamName)');
        $this->addSql('ALTER TABLE way_point CHANGE locationName locationName VARCHAR(150) NOT NULL');
        $this->addSql('CREATE INDEX idx_wayPoint_locationName ON way_point (locationName)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_name_teamName ON walk');
        $this->addSql('DROP INDEX idx_wayPoint_locationName ON way_point');
        $this->addSql('ALTER TABLE team CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE name name VARCHAR(1024) NOT NULL');
        $this->addSql('ALTER TABLE way_point CHANGE locationName locationName VARCHAR(4096) NOT NULL');
    }
}
