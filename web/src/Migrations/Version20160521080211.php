<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160521080211 extends AbstractMigration
{
    #[\Override]
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' != $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Gewalt (körperlich)',
                'red'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Gewalt (verbal)',
                'orange'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Drogen',
                'orange'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Partnerschaft',
                'blue'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Jobcenter',
                'yellow'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Ausbildung',
                'yellow'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Sexualität',
                'blue'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Politik',
                'green'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Wohnungslosigkeit',
                'yellow'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Schwangerschaft',
                'orange'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Polizei',
                'blue'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Grundversorgung',
                'yellow'
            )
        );
        $this->addSql(
            sprintf(
                'INSERT INTO tag (name, color) VALUES ("%s", "%s")',
                'Kindeswohl',
                'red'
            )
        );
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' != $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            sprintf(
                'DELETE FROM tag WHERE name in (\'%s\')',
                implode(
                    '\', \'',
                    [
                        'Gewalt (körperlich)',
                        'Gewalt (verbal)',
                        'Drogen',
                        'Partnerschaft',
                        'Jobcenter',
                        'Ausbildung',
                        'Sexualität',
                        'Politik',
                        'Wohnungslosigkeit',
                        'Schwangerschaft',
                        'Polizei',
                        'Grundversorgung',
                    ]
                )
            )
        );
    }
}
