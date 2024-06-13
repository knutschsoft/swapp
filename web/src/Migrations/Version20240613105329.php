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
        $this->addSql('CREATE INDEX idx_walk_name_teamName ON walk (name, teamName)');
        $this->addSql('CREATE INDEX idx_search ON way_point (locationName, note, oneOnOneInterview)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_walk_name_teamName ON walk');
        $this->addSql('DROP INDEX idx_search ON way_point');
    }
}
