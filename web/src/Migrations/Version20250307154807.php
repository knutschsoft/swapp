<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250307154807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add isWithWeather';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD isWithWeather TINYINT(1) NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE walk ADD isWithWeather TINYINT(1) NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE team CHANGE isWithWeather isWithWeather TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE isWithWeather isWithWeather TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP isWithWeather');
        $this->addSql('ALTER TABLE walk DROP isWithWeather');
    }
}
