<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220224100754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE walk CHANGE name name VARCHAR(1024) NOT NULL');
        $this->addSql('UPDATE walk SET insights=\'\' WHERE insights IS NULL');
        $this->addSql('UPDATE walk SET commitments=\'\' WHERE commitments IS NULL');
        $this->addSql('ALTER TABLE walk CHANGE insights insights TEXT NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE commitments commitments TEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE walk CHANGE name name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE insights insights VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE commitments commitments VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
    }
}
