<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250416071418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add NOT NULL for foreign keys.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE systemic_question CHANGE client_id client_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tag CHANGE client_id client_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team CHANGE client_id client_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE client_id client_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE walk CHANGE client_id client_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE way_point CHANGE walk_id walk_id INT NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE systemic_question CHANGE client_id client_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tag CHANGE client_id client_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE team CHANGE client_id client_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE client_id client_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE walk CHANGE client_id client_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE way_point CHANGE walk_id walk_id INT DEFAULT NULL
        SQL);
    }
}
