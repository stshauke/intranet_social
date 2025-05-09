<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250503120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes created_at et updated_at à la table work_group';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE work_group ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE work_group DROP created_at, DROP updated_at');
    }
}
