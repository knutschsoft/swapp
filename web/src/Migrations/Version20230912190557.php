<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230912190557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE walk ADD walkCreator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE walk ADD CONSTRAINT FK_8D917A551E24366C FOREIGN KEY (walkCreator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D917A551E24366C ON walk (walkCreator_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE walk DROP FOREIGN KEY FK_8D917A551E24366C');
        $this->addSql('DROP INDEX IDX_8D917A551E24366C ON walk');
        $this->addSql('ALTER TABLE walk DROP walkCreator_id');
    }
}
