<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Cleaned version to avoid duplication errors
 */
final class Version20250409171504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Clean migration: add creator foreign key and index (created_at and updated_at already exist)';
    }

    public function up(Schema $schema): void
    {
        // ✅ On vérifie que creator_id est nullable et on ajoute la contrainte proprement
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group CHANGE creator_id creator_id INT DEFAULT NULL
        SQL);

        // ✅ On ajoute la foreign key vers la table user (creator)
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group ADD CONSTRAINT FK_453B3FEA61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)
        SQL);

        // ✅ On ajoute l’index sur creator_id
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_453B3FEA61220EA6 ON work_group (creator_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // ✅ Rollback propre
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group DROP FOREIGN KEY FK_453B3FEA61220EA6
        SQL);

        $this->addSql(<<<'SQL'
            DROP INDEX IDX_453B3FEA61220EA6 ON work_group
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE work_group CHANGE creator_id creator_id INT NOT NULL
        SQL);
    }
}
