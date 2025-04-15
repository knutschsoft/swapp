<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250415181443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove tags for walk because they are unused at all.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE tag_walk DROP FOREIGN KEY FK_639EC5E3BAD26311
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tag_walk DROP FOREIGN KEY FK_639EC5E35EEE1B48
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tag_walk
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE tag_walk (tag_id INT NOT NULL, walk_id INT NOT NULL, INDEX IDX_639EC5E3BAD26311 (tag_id), INDEX IDX_639EC5E35EEE1B48 (walk_id), PRIMARY KEY(tag_id, walk_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tag_walk ADD CONSTRAINT FK_639EC5E3BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tag_walk ADD CONSTRAINT FK_639EC5E35EEE1B48 FOREIGN KEY (walk_id) REFERENCES walk (id) ON DELETE CASCADE
        SQL);
    }
}
