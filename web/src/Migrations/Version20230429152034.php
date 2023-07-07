<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230429152034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD isWithSystemicQuestion TINYINT(1) NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE team ALTER isWithSystemicQuestion DROP DEFAULT');
        $this->addSql('ALTER TABLE walk ADD isWithSystemicQuestion TINYINT(1) NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE walk ALTER isWithSystemicQuestion DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP isWithSystemicQuestion');
        $this->addSql('ALTER TABLE walk DROP isWithSystemicQuestion');
    }
}
