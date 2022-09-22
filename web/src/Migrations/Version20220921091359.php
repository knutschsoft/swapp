<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220921091359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD isWithAgeRanges TINYINT(1) NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE walk ADD isWithAgeRanges TINYINT(1) NOT NULL DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP isWithAgeRanges');
        $this->addSql('ALTER TABLE walk DROP isWithAgeRanges');
    }
}
