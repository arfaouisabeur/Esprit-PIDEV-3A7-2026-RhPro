<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds demande_service.type → type_service.id (Doctrine ManyToOne mapping on DemandeService::$type).
 */
final class Version20260409180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add nullable FK column `type` on demande_service referencing type_service(id).';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        if (!$sm->tablesExist(['demande_service']) || !$sm->tablesExist(['type_service'])) {
            return;
        }

        foreach ($sm->listTableColumns('demande_service') as $column) {
            if (strtolower($column->getName()) === 'type') {
                return;
            }
        }

        $this->addSql('ALTER TABLE demande_service ADD `type` INT DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE demande_service ADD CONSTRAINT FK_DS_TYPE_SERVICE '
            . 'FOREIGN KEY (`type`) REFERENCES type_service (id) ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        if (!$sm->tablesExist(['demande_service'])) {
            return;
        }

        $hasType = false;
        foreach ($sm->listTableColumns('demande_service') as $column) {
            if (strtolower($column->getName()) === 'type') {
                $hasType = true;
                break;
            }
        }
        if (!$hasType) {
            return;
        }

        $this->addSql('ALTER TABLE demande_service DROP FOREIGN KEY FK_DS_TYPE_SERVICE');
        $this->addSql('ALTER TABLE demande_service DROP COLUMN `type`');
    }
}
