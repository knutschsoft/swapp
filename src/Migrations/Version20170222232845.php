<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170222232845 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE walk CHANGE walkReflection walkReflection VARCHAR(4096) NOT NULL');
        $this->addSql('ALTER TABLE systemic_question CHANGE question question VARCHAR(4096) NOT NULL');
        $this->addSql('ALTER TABLE way_point CHANGE note note VARCHAR(4096) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE systemic_question CHANGE question question VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE walk CHANGE walkReflection walkReflection VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE way_point CHANGE note note VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
