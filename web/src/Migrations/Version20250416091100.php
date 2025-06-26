<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250416091100 extends AbstractMigration
{
    private array $fieldsToConvert = [
        'user' => ['roles'],
        'walk' => ['guestNames'],
        'team' => ['guestNames', 'locationNames'],
    ];

    public function getDescription(): string
    {
        return 'Change columns of type array to json.';
    }

    public function up(Schema $schema): void
    {
        // temporary fields
        foreach ($this->fieldsToConvert as $table => $fields) {
            foreach ($fields as $field) {
                $this->addSql(\sprintf(
                    'ALTER TABLE `%s` ADD `%s_json` LONGTEXT DEFAULT NULL',
                    $table,
                    $field
                ));
            }
        }

        $this->addSql(<<<'SQL'
            ALTER TABLE team CHANGE ageRanges ageRanges JSON NOT NULL, CHANGE userGroupNames userGroupNames JSON NOT NULL, CHANGE consumableNames consumableNames JSON NOT NULL, CHANGE counselingNames counselingNames JSON NOT NULL, CHANGE medicalNames medicalNames JSON NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE walk CHANGE ageRanges ageRanges JSON NOT NULL, CHANGE userGroupNames userGroupNames JSON NOT NULL, CHANGE consumableNames consumableNames JSON NOT NULL, CHANGE counselingNames counselingNames JSON NOT NULL, CHANGE medicalNames medicalNames JSON NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE way_point CHANGE ageGroups ageGroups JSON NOT NULL, CHANGE userGroups userGroups JSON NOT NULL, CHANGE consumables consumables JSON NOT NULL, CHANGE counselings counselings JSON NOT NULL, CHANGE medicals medicals JSON NOT NULL
        SQL);
    }

    public function postUp(Schema $schema): void
    {
        $connection = $this->connection;

        foreach ($this->fieldsToConvert as $table => $fields) {
            $this->write(sprintf('Verarbeite Tabelle "%s"...', $table));

            $selectFields = implode(', ', array_merge(['id'], $fields));
            $rows = $connection->fetchAllAssociative(sprintf(
                'SELECT %s FROM `%s`',
                $selectFields,
                $table
            ));

            foreach ($rows as $row) {
                $updateData = [];
                $params = ['id' => $row['id']];

                foreach ($fields as $field) {
                    $serialized = $row[$field];
                    try {
                        $unserialized = @unserialize($serialized);

                        if (!is_array($unserialized)) {
                            $this->write("WARN: $table.id={$row['id']} Feld '$field' ist kein Array.");
                            continue;
                        }

                        $json = json_encode($unserialized, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                        $updateData[] = sprintf('`%s_json` = :%s_json', $field, $field);
                        $params["{$field}_json"] = $json;
                    } catch (\Throwable $e) {
                        $this->write("FEHLER: $table.id={$row['id']} Feld '$field': " . $e->getMessage());
                    }
                }

                if (!empty($updateData)) {
                    $connection->executeStatement(
                        sprintf('UPDATE `%s` SET %s WHERE id = :id', $table, implode(', ', $updateData)),
                        $params
                    );
                }
            }

            // Jetzt alte Spalten droppen und json-Spalten korrekt benennen
            foreach ($fields as $field) {
                $connection->executeStatement(sprintf('ALTER TABLE `%s` DROP COLUMN `%s`', $table, $field));
                $connection->executeStatement(sprintf('ALTER TABLE `%s` CHANGE `%s_json` `%s` JSON NOT NULL', $table, $field, $field));
            }
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE team CHANGE guestNames guestNames LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE locationNames locationNames LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE ageRanges ageRanges JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE consumableNames consumableNames JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE counselingNames counselingNames JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE medicalNames medicalNames JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE userGroupNames userGroupNames JSON NOT NULL COMMENT '(DC2Type:json_document)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE walk CHANGE guestNames guestNames LONGTEXT NOT NULL COMMENT '(DC2Type:array)', CHANGE ageRanges ageRanges JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE consumableNames consumableNames JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE counselingNames counselingNames JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE medicalNames medicalNames JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE userGroupNames userGroupNames JSON NOT NULL COMMENT '(DC2Type:json_document)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE way_point CHANGE ageGroups ageGroups JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE userGroups userGroups JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE consumables consumables JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE counselings counselings JSON NOT NULL COMMENT '(DC2Type:json_document)', CHANGE medicals medicals JSON NOT NULL COMMENT '(DC2Type:json_document)'
        SQL);
    }
}
