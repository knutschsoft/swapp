<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220620040149 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD userGroupNames JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE walk ADD userGroupNames JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE way_point ADD userGroups JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('UPDATE team SET userGroupNames = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE walk SET userGroupNames = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE way_point SET userGroups = \'[]\' WHERE id <> \'\'');
        $this->addSql('ALTER TABLE walk ADD isWithUserGroups TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE walk CHANGE isWithUserGroups isWithUserGroups TINYINT(1) NOT NULL');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team DROP userGroupNames');
        $this->addSql('ALTER TABLE walk DROP userGroupNames');
        $this->addSql('ALTER TABLE way_point DROP userGroups');
        $this->addSql('ALTER TABLE walk DROP isWithUserGroups');
    }
}
