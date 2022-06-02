<?php
declare(strict_types=1);

namespace Application\Migrations;

use App\Value\AgeGroup;
use App\Value\AgeRange;
use App\Value\Gender;
use App\Value\PeopleCount;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\AbortMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190211053518 extends AbstractMigration
{
    private $wayPoints = [];

    public function preUp(Schema $schema): void
    {
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $query = $this->connection->createQueryBuilder()
            ->select(
                'id, malesChildCount, femalesChildCount, malesKidCount, femalesKidCount, malesYoungAdultCount, femalesYoungAdultCount, malesYouthCount, femalesYouthCount, malesAdultCount, femalesAdultCount'
            )
            ->from('way_point')
            ->getSQL();

        $this->wayPoints = $this->connection->executeQuery($query)->fetchAllAssociative();
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(
            'ALTER TABLE way_point ADD ageGroups JSON NOT NULL COMMENT \'(DC2Type:json_document)\', DROP malesChildCount, DROP femalesChildCount, DROP malesKidCount, DROP femalesKidCount, DROP malesYoungAdultCount, DROP femalesYoungAdultCount, DROP malesYouthCount, DROP femalesYouthCount, DROP malesAdultCount, DROP femalesAdultCount'
        );
    }

    public function postUp(Schema $schema): void
    {
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        foreach ($this->wayPoints as $wayPoint) {
            $id = $wayPoint['id'];

            $ageGroups = $this->getAgeGroups($wayPoint);
            $ageGroups = $this->connection->convertToDatabaseValue($ageGroups, 'json_document');

            // need to do some conversions
            $ageGroups = str_replace('\\', '\\\\', $ageGroups);
            $ageGroups = '\''.$ageGroups.'\'';

            $query = $this->connection->createQueryBuilder()
                ->update('way_point')
                ->set('way_point.ageGroups', $ageGroups)
                ->where('way_point.id = '.$this->connection->convertToDatabaseValue($id, 'integer'))
                ->getSQL();

            $this->connection->executeQuery(
                $query
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'ALTER TABLE way_point ADD malesChildCount SMALLINT NOT NULL, ADD femalesChildCount SMALLINT NOT NULL, ADD malesKidCount SMALLINT NOT NULL, ADD femalesKidCount SMALLINT NOT NULL, ADD malesYoungAdultCount SMALLINT NOT NULL, ADD femalesYoungAdultCount SMALLINT NOT NULL, ADD malesYouthCount SMALLINT NOT NULL, ADD femalesYouthCount SMALLINT NOT NULL, ADD malesAdultCount SMALLINT NOT NULL, ADD femalesAdultCount SMALLINT NOT NULL, DROP ageGroups'
        );
    }

    private function getAgeGroups(array $wayPoint): array
    {
        $ageGroups = [];
        foreach ($wayPoint as $key => $value) {
            if ('id' === $key) {
                continue;
            }

            $ageGroups[] = AgeGroup::fromRangeGenderAndCount(
                $this->getRange($key),
                $this->getGender($key),
                PeopleCount::fromInt((int) $value)
            );
        }

        return $ageGroups;
    }

    private function getRange(string $oldInternalName): AgeRange
    {
        switch ($oldInternalName) {
            case 'malesChildCount':
            case 'femalesChildCount':
                $start = 6;
                $end = 11;

                break;
            case 'malesKidCount':
            case 'femalesKidCount':
                $start = 0;
                $end = 5;

                break;
            case 'malesYoungAdultCount':
            case 'femalesYoungAdultCount':
                $start = 18;
                $end = 21;

                break;
            case 'malesYouthCount':
            case 'femalesYouthCount':
                $start = 12;
                $end = 17;

                break;
            case 'malesAdultCount':
            case 'femalesAdultCount':
                $start = 22;
                $end = 27;

                break;
            default:
                throw new AbortMigration('Unknown old internal name in migration: '.$oldInternalName);
        }

        return AgeRange::fromArray(['start' => $start, 'end' => $end]);
    }

    private function getGender(string $oldInternalName): Gender
    {
        switch ($oldInternalName) {
            case 'malesKidCount':
            case 'malesChildCount':
            case 'malesYoungAdultCount':
            case 'malesYouthCount':
            case 'malesAdultCount':
                $gender = Gender::GENDER_MALE;

                break;
            case 'femalesChildCount':
            case 'femalesYoungAdultCount':
            case 'femalesYouthCount':
            case 'femalesKidCount':
            case 'femalesAdultCount':
                $gender = Gender::GENDER_FEMALE;

                break;
            default:
                throw new AbortMigration('Unknown old internal name in migration: '.$oldInternalName);
        }

        return Gender::fromString($gender);
    }
}
