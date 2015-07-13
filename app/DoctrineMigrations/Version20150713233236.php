<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150713233236 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE walk (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, startTime DATETIME NOT NULL, endTime DATETIME NOT NULL, walkReflection VARCHAR(255) NOT NULL, rating SMALLINT NOT NULL, systemicQuestion VARCHAR(4096) NOT NULL, systemicAnswer VARCHAR(4096) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE walk_user (walk_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_563B92D05EEE1B48 (walk_id), INDEX IDX_563B92D0A76ED395 (user_id), PRIMARY KEY(walk_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE walk_tag (walk_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_1004F8A95EEE1B48 (walk_id), INDEX IDX_1004F8A9BAD26311 (tag_id), PRIMARY KEY(walk_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE way_point (id INT AUTO_INCREMENT NOT NULL, walk_id INT DEFAULT NULL, locationName VARCHAR(4096) NOT NULL, ageRangeStart SMALLINT NOT NULL, ageRangeEnd SMALLINT NOT NULL, malesCount SMALLINT NOT NULL, femalesCount SMALLINT NOT NULL, note VARCHAR(255) NOT NULL, INDEX IDX_9B2531745EEE1B48 (walk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE walk_user ADD CONSTRAINT FK_563B92D05EEE1B48 FOREIGN KEY (walk_id) REFERENCES walk (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE walk_user ADD CONSTRAINT FK_563B92D0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE walk_tag ADD CONSTRAINT FK_1004F8A95EEE1B48 FOREIGN KEY (walk_id) REFERENCES walk (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE walk_tag ADD CONSTRAINT FK_1004F8A9BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE way_point ADD CONSTRAINT FK_9B2531745EEE1B48 FOREIGN KEY (walk_id) REFERENCES walk (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE walk_tag DROP FOREIGN KEY FK_1004F8A9BAD26311');
        $this->addSql('ALTER TABLE walk_user DROP FOREIGN KEY FK_563B92D05EEE1B48');
        $this->addSql('ALTER TABLE walk_tag DROP FOREIGN KEY FK_1004F8A95EEE1B48');
        $this->addSql('ALTER TABLE way_point DROP FOREIGN KEY FK_9B2531745EEE1B48');
        $this->addSql('ALTER TABLE walk_user DROP FOREIGN KEY FK_563B92D0A76ED395');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE walk');
        $this->addSql('DROP TABLE walk_user');
        $this->addSql('DROP TABLE walk_tag');
        $this->addSql('DROP TABLE way_point');
        $this->addSql('DROP TABLE user');
    }
}
