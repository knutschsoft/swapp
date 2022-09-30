<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220923073822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD guestNames LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE isWithAgeRanges isWithAgeRanges TINYINT(1) NOT NULL');
        $this->addSql('UPDATE team SET guestNames = \'a:0:{}\'');
        $this->addSql('ALTER TABLE team ADD isWithGuests TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE walk ADD isWithGuests TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE walk CHANGE isWithAgeRanges isWithAgeRanges TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE team CHANGE isWithGuests isWithGuests TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE isWithGuests isWithGuests TINYINT(1) NOT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP guestNames, CHANGE isWithAgeRanges isWithAgeRanges TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE isWithAgeRanges isWithAgeRanges TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE team DROP isWithGuests');
        $this->addSql('ALTER TABLE walk DROP isWithGuests');
    }
}
