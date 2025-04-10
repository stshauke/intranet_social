<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration corrigée propre pour éviter les erreurs de doublons de colonnes et de tables existantes
 */
final class Version20250409164432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Correction migration WorkGroup : suppression des erreurs pour colonnes et tables existantes';
    }

    public function up(Schema $schema): void
    {
        // ✅ Pas de création de la table work_group_user (elle existe déjà)

        // ✅ Pas de modification du champ creator_id (déjà existant)

        // ✅ Modification de la description si besoin
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group 
            CHANGE description description VARCHAR(255) NOT NULL
        SQL);

        // ✅ Pas besoin de recréer la clé étrangère creator_id (elle existe déjà)
    }

    public function down(Schema $schema): void
    {
        // ✅ Rien à faire dans le down car les colonnes existent déjà
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group 
            CHANGE description description LONGTEXT DEFAULT NULL
        SQL);
    }
}
