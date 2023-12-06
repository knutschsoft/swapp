<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200210170149 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE walk DROP FOREIGN KEY FK_8D917A55638AAAEC');
        $this->addSql('DROP INDEX IDX_8D917A55638AAAEC ON walk');
        $this->addSql('ALTER TABLE walk ADD systemicQuestion VARCHAR(4096) NOT NULL');

        $questions = $this->connection->createQueryBuilder()->select('q.id, q.question')
            ->from('systemic_question', 'q')
            ->execute()
            ->fetchAll();

        foreach ($questions as $question) {
            $this->addSql(
                \sprintf(
                    'UPDATE walk SET walk.systemicQuestion = "%s" WHERE walk.systemicQuestion_id = %d',
                    $question['question'],
                    $question['id']
                )
            );
        }

        $this->addSql('ALTER TABLE walk DROP systemicQuestion_id');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE walk ADD systemicQuestion_id INT DEFAULT NULL');

        $questions = $this->connection->createQueryBuilder()->select('q.id, q.question')
            ->from('systemic_question', 'q')
            ->execute()
            ->fetchAll();

        foreach ($questions as $question) {
            $this->addSql(
                \sprintf(
                    'UPDATE walk SET walk.systemicQuestion_id = %d WHERE walk.systemicQuestion = "%s"',
                    $question['id'],
                    $question['question']
                )
            );
        }

        $this->addSql('ALTER TABLE walk DROP systemicQuestion');
        $this->addSql('ALTER TABLE walk ADD CONSTRAINT FK_8D917A55638AAAEC FOREIGN KEY (systemicQuestion_id) REFERENCES systemic_question (id)');
        $this->addSql('CREATE INDEX IDX_8D917A55638AAAEC ON walk (systemicQuestion_id)');
    }
}
