<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311043124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_to_categories (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, categories_id INT DEFAULT NULL, INDEX IDX_2B11A064B89032C (post_id), INDEX IDX_2B11A06A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_to_categories ADD CONSTRAINT FK_2B11A064B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_to_categories ADD CONSTRAINT FK_2B11A06A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_to_categories DROP FOREIGN KEY FK_2B11A064B89032C');
        $this->addSql('ALTER TABLE post_to_categories DROP FOREIGN KEY FK_2B11A06A21214B7');
        $this->addSql('DROP TABLE post_to_categories');
    }
}
