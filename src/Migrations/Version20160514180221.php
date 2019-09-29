<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160514180221 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_walk (tag_id INT NOT NULL, walk_id INT NOT NULL, INDEX IDX_639EC5E3BAD26311 (tag_id), INDEX IDX_639EC5E35EEE1B48 (walk_id), PRIMARY KEY(tag_id, walk_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_waypoint (tag_id INT NOT NULL, waypoint_id INT NOT NULL, INDEX IDX_85ED235ABAD26311 (tag_id), INDEX IDX_85ED235A7BB1FD97 (waypoint_id), PRIMARY KEY(tag_id, waypoint_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE walk (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, startTime DATETIME NOT NULL, endTime DATETIME NOT NULL, walkReflection VARCHAR(255) NOT NULL, rating SMALLINT NOT NULL, systemicAnswer VARCHAR(4096) NOT NULL, insights VARCHAR(255) DEFAULT NULL, commitments VARCHAR(255) DEFAULT NULL, isResubmission TINYINT(1) NOT NULL, weather VARCHAR(255) NOT NULL, holidays TINYINT(1) NOT NULL, conceptOfDay VARCHAR(4096) NOT NULL, teamName VARCHAR(255) NOT NULL, deletedAt DATETIME DEFAULT NULL, systemicQuestion_id INT DEFAULT NULL, INDEX IDX_8D917A55638AAAEC (systemicQuestion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE systemic_question (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(255) NOT NULL, deletedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE way_point (id INT AUTO_INCREMENT NOT NULL, walk_id INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, locationName VARCHAR(4096) NOT NULL, malesChildCount SMALLINT NOT NULL, femalesChildCount SMALLINT NOT NULL, malesKidCount SMALLINT NOT NULL, femalesKidCount SMALLINT NOT NULL, malesYoungAdultCount SMALLINT NOT NULL, femalesYoungAdultCount SMALLINT NOT NULL, malesYouthCount SMALLINT NOT NULL, femalesYouthCount SMALLINT NOT NULL, malesAdultCount SMALLINT NOT NULL, femalesAdultCount SMALLINT NOT NULL, note VARCHAR(255) NOT NULL, isMeeting TINYINT(1) NOT NULL, INDEX IDX_9B2531745EEE1B48 (walk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guest (id INT AUTO_INCREMENT NOT NULL, walk_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_ACB79A355EEE1B48 (walk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_walk (user_id INT NOT NULL, walk_id INT NOT NULL, INDEX IDX_F710369CA76ED395 (user_id), INDEX IDX_F710369C5EEE1B48 (walk_id), PRIMARY KEY(user_id, walk_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_walk ADD CONSTRAINT FK_639EC5E3BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_walk ADD CONSTRAINT FK_639EC5E35EEE1B48 FOREIGN KEY (walk_id) REFERENCES walk (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_waypoint ADD CONSTRAINT FK_85ED235ABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_waypoint ADD CONSTRAINT FK_85ED235A7BB1FD97 FOREIGN KEY (waypoint_id) REFERENCES way_point (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE walk ADD CONSTRAINT FK_8D917A55638AAAEC FOREIGN KEY (systemicQuestion_id) REFERENCES systemic_question (id)');
        $this->addSql('ALTER TABLE way_point ADD CONSTRAINT FK_9B2531745EEE1B48 FOREIGN KEY (walk_id) REFERENCES walk (id)');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A355EEE1B48 FOREIGN KEY (walk_id) REFERENCES walk (id)');
        $this->addSql('ALTER TABLE user_walk ADD CONSTRAINT FK_F710369CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_walk ADD CONSTRAINT FK_F710369C5EEE1B48 FOREIGN KEY (walk_id) REFERENCES walk (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tag_walk DROP FOREIGN KEY FK_639EC5E3BAD26311');
        $this->addSql('ALTER TABLE tag_waypoint DROP FOREIGN KEY FK_85ED235ABAD26311');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE tag_walk DROP FOREIGN KEY FK_639EC5E35EEE1B48');
        $this->addSql('ALTER TABLE way_point DROP FOREIGN KEY FK_9B2531745EEE1B48');
        $this->addSql('ALTER TABLE guest DROP FOREIGN KEY FK_ACB79A355EEE1B48');
        $this->addSql('ALTER TABLE user_walk DROP FOREIGN KEY FK_F710369C5EEE1B48');
        $this->addSql('ALTER TABLE walk DROP FOREIGN KEY FK_8D917A55638AAAEC');
        $this->addSql('ALTER TABLE tag_waypoint DROP FOREIGN KEY FK_85ED235A7BB1FD97');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232A76ED395');
        $this->addSql('ALTER TABLE user_walk DROP FOREIGN KEY FK_F710369CA76ED395');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_walk');
        $this->addSql('DROP TABLE tag_waypoint');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE walk');
        $this->addSql('DROP TABLE systemic_question');
        $this->addSql('DROP TABLE way_point');
        $this->addSql('DROP TABLE guest');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_walk');
    }
}
