<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20230428111526 extends AbstractMigration
{
    private array $walks = [];

    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function preUp(Schema $schema): void
    {
        $query = $this->connection->createQueryBuilder()
            ->select(
                'id, conceptOfDay'
            )
            ->from('walk')
            ->getSQL();

        $this->walks = $this->connection->executeQuery($query)->fetchAllAssociative();
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE walk SET conceptOfDay=\'[]\' WHERE id > 0;');
        $this->addSql('ALTER TABLE walk CHANGE conceptOfDay conceptOfDay JSON NOT NULL;');
    }

    #[\Override]
    public function postUp(Schema $schema): void
    {
        foreach ($this->walks as $walk) {
            $id = $walk['id'];
            $conceptOfDayString = $walk['conceptOfDay'];

            $conceptOfDayArray = \explode(PHP_EOL, (string) $conceptOfDayString);
            $conceptOfDayArray = \array_map('trim', $conceptOfDayArray);
            $conceptOfDayArray = \array_filter($conceptOfDayArray);
            $value = \str_replace('\\"', '\\\\"', (string) $this->connection->convertToDatabaseValue($conceptOfDayArray, Types::JSON));
            $value = \str_replace('\\u00', '\\\\\\u00', $value);
            $value = \str_replace('\'', '\\\'', $value);

            $query = $this->connection->createQueryBuilder()
                ->update('walk')
                ->set('walk.conceptOfDay', "'".$value."'")
                ->where('walk.id = '.$this->connection->convertToDatabaseValue($id, 'integer'))
                ->getSQL();

            $this->connection->executeQuery(
                $query
            );
        }
    }


    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE walk CHANGE conceptOfDay conceptOfDay VARCHAR(4096) NOT NULL');
    }
}
