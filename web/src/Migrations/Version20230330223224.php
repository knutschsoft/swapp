<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230330223224 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tag ADD isEnabled TINYINT(1) NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE tag CHANGE isEnabled isEnabled TINYINT(1) NOT NULL');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tag DROP isEnabled');
    }
}
