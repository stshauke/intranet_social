<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503164056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE work_group_user (work_group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BFAA20EA2BE1531B (work_group_id), INDEX IDX_BFAA20EAA76ED395 (user_id), PRIMARY KEY(work_group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group_user ADD CONSTRAINT FK_BFAA20EA2BE1531B FOREIGN KEY (work_group_id) REFERENCES work_group (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group_user ADD CONSTRAINT FK_BFAA20EAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group ADD CONSTRAINT FK_453B3FEA61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group_user DROP FOREIGN KEY FK_BFAA20EA2BE1531B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group_user DROP FOREIGN KEY FK_BFAA20EAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE work_group_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group DROP FOREIGN KEY FK_453B3FEA61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_group CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL
        SQL);
    }
}
