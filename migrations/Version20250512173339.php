<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512173339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE favorite_group (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_DB486FE1A76ED395 (user_id), INDEX IDX_DB486FE1FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group ADD CONSTRAINT FK_DB486FE1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group ADD CONSTRAINT FK_DB486FE1FE54D947 FOREIGN KEY (group_id) REFERENCES work_group (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group DROP FOREIGN KEY FK_DB486FE1A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite_group DROP FOREIGN KEY FK_DB486FE1FE54D947
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favorite_group
        SQL);
    }
}
