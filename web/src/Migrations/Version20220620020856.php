<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220620020856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE walk ADD isUnfinished TINYINT(1) NOT NULL DEFAULT 1');
        $this->addSql('UPDATE walk SET isUnfinished = 0 WHERE systemicAnswer <> \'\' AND endTime <> startTime');
        $this->addSql('ALTER TABLE walk CHANGE isUnfinished isUnfinished TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE endTime endTime DATETIME DEFAULT NULL');
        $this->addSql('UPDATE walk SET endTime = NULL WHERE isUnfinished = 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE walk DROP isUnfinished');
    }
}
