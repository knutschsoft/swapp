<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250703104807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add isWithHolidays';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD isWithHolidays TINYINT(1) NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE walk ADD isWithHolidays TINYINT(1) NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE team CHANGE isWithHolidays isWithHolidays TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE isWithHolidays isWithHolidays TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE holidays holidays TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP isWithHolidays');
        $this->addSql('ALTER TABLE walk DROP isWithHolidays');
        $this->addSql('ALTER TABLE walk CHANGE holidays holidays TINYINT(1) NOT NULL');
    }
}
