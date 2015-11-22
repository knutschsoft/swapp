<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151122231829 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE walk ADD systemicQuestion_id INT DEFAULT NULL, DROP systemicQuestion, CHANGE weather weather VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE walk ADD CONSTRAINT FK_8D917A55638AAAEC FOREIGN KEY (systemicQuestion_id) REFERENCES systemic_question (id)');
        $this->addSql('CREATE INDEX IDX_8D917A55638AAAEC ON walk (systemicQuestion_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE walk DROP FOREIGN KEY FK_8D917A55638AAAEC');
        $this->addSql('DROP INDEX IDX_8D917A55638AAAEC ON walk');
        $this->addSql('ALTER TABLE walk ADD systemicQuestion VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP systemicQuestion_id, CHANGE weather weather TINYINT(1) NOT NULL');
    }
}
