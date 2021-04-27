<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200619171023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        // $this->addSql('DROP TABLE tag_way_point');

        //$this->addSql('ALTER TABLE team CHANGE ageRanges ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        //$this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        //$this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        //$this->addSql(
        //    'ALTER TABLE way_point CHANGE ageGroups ageGroups JSON NOT NULL COMMENT \'(DC2Type:json_document)\', CHANGE createdAt createdAt DATETIME NOT NULL, CHANGE updatedAt updatedAt DATETIME NOT NULL'
        //);
        //$this->addSql('ALTER TABLE walk CHANGE ageRanges ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        //$this->addSql(
        //    'CREATE TABLE tag_way_point (tag_id INT NOT NULL, way_point_id INT NOT NULL, INDEX IDX_8A120B53B8E0D6D2 (way_point_id), INDEX IDX_8A120B53BAD26311 (tag_id), PRIMARY KEY(tag_id, way_point_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        //);
        //$this->addSql(
        //    'ALTER TABLE tag_way_point ADD CONSTRAINT FK_8A120B53B8E0D6D2 FOREIGN KEY (way_point_id) REFERENCES way_point (id) ON DELETE CASCADE'
        //);
        //$this->addSql(
        //    'ALTER TABLE tag_way_point ADD CONSTRAINT FK_8A120B53BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE'
        //);
        $this->addSql('DROP TABLE refresh_tokens');
        //$this->addSql('ALTER TABLE team CHANGE ageRanges ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        //$this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        //$this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        //$this->addSql('ALTER TABLE walk CHANGE ageRanges ageRanges JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        //$this->addSql(
        //    'ALTER TABLE way_point CHANGE ageGroups ageGroups JSON NOT NULL COMMENT \'(DC2Type:json_document)\', CHANGE createdAt createdAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updatedAt updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL'
        //);
    }
}
