<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301041237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE crud_detail (id INT AUTO_INCREMENT NOT NULL, crud_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type INT NOT NULL, setting JSON DEFAULT NULL, INDEX IDX_F4C17BC57DAE3605 (crud_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE crud_detail ADD CONSTRAINT FK_F4C17BC57DAE3605 FOREIGN KEY (crud_id) REFERENCES crud (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE crud_detail DROP FOREIGN KEY FK_F4C17BC57DAE3605');
        $this->addSql('DROP TABLE crud_detail');
    }
}
