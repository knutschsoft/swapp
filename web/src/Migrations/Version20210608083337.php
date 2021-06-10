<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210608083337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE systemic_question ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE systemic_question ADD CONSTRAINT FK_D2685EE319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_D2685EE319EB6921 ON systemic_question (client_id)');
        $this->addSql('ALTER TABLE tag ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B78319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_389B78319EB6921 ON tag (client_id)');
        $this->addSql('ALTER TABLE team ADD client_id INT DEFAULT NULL, CHANGE ageRanges ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F19EB6921 ON team (client_id)');
        $this->addSql('ALTER TABLE user ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE INDEX IDX_8D93D64919EB6921 ON user (client_id)');
        $this->addSql('ALTER TABLE walk ADD client_id INT DEFAULT NULL, CHANGE ageRanges ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE walk ADD CONSTRAINT FK_8D917A5519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_8D917A5519EB6921 ON walk (client_id)');
        $this->addSql(
            'ALTER TABLE way_point CHANGE ageGroups ageGroups JSON NOT NULL COMMENT \'(DC2Type:json_document)\', CHANGE createdAt createdAt DATETIME NOT NULL, CHANGE updatedAt updatedAt DATETIME NOT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE systemic_question DROP FOREIGN KEY FK_D2685EE319EB6921');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B78319EB6921');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F19EB6921');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64919EB6921');
        $this->addSql('ALTER TABLE walk DROP FOREIGN KEY FK_8D917A5519EB6921');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP INDEX IDX_D2685EE319EB6921 ON systemic_question');
        $this->addSql('ALTER TABLE systemic_question DROP client_id');
        $this->addSql('DROP INDEX IDX_389B78319EB6921 ON tag');
        $this->addSql('ALTER TABLE tag DROP client_id');
        $this->addSql('DROP INDEX IDX_C4E0A61F19EB6921 ON team');
        $this->addSql('ALTER TABLE team DROP client_id, CHANGE ageRanges ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('DROP INDEX IDX_8D93D64919EB6921 ON user');
        $this->addSql('ALTER TABLE user DROP client_id');
        $this->addSql('DROP INDEX IDX_8D917A5519EB6921 ON walk');
        $this->addSql('ALTER TABLE walk DROP client_id, CHANGE ageRanges ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql(
            'ALTER TABLE way_point CHANGE ageGroups ageGroups JSON NOT NULL COMMENT \'(DC2Type:json_document)\', CHANGE createdAt createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updatedAt updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL'
        );
    }
}
