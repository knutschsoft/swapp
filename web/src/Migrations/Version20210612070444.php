<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210612070444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE systemic_question ADD createdAt DATETIME NOT NULL DEFAULT NOW(), ADD updatedAt DATETIME NOT NULL DEFAULT NOW(), ADD isEnabled BOOLEAN NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE systemic_question ALTER createdAt DROP DEFAULT');
        $this->addSql('ALTER TABLE systemic_question ALTER updatedAt DROP DEFAULT');
        $this->addSql('ALTER TABLE systemic_question ALTER isEnabled DROP DEFAULT');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE systemic_question DROP createdAt, DROP updatedAt, DROP isEnabled');
    }
}
