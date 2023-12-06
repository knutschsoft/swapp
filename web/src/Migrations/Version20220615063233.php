<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220615063233 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD isWithUserGroups TINYINT(1) NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE team CHANGE isWithUserGroups isWithUserGroups TINYINT(1) NOT NULL');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP isWithUserGroups');
    }
}
