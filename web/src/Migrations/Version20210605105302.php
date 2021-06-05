<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210605105302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649C05FB297 ON user');
        $this->addSql('UPDATE user SET confirmation_token = "01234567890123456789012345678912" WHERE confirmation_token IS NULL');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(180) NOT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(180) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
    }
}
