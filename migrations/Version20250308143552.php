<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308143552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_statistic (id INT AUTO_INCREMENT NOT NULL, ip_address VARCHAR(32) DEFAULT NULL, datetime DATETIME DEFAULT NULL, agent VARCHAR(128) NOT NULL, agent_detail VARCHAR(255) NOT NULL, platform VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post ADD post_statistic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF88B5B5C FOREIGN KEY (post_statistic_id) REFERENCES post_statistic (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF88B5B5C ON post (post_statistic_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF88B5B5C');
        $this->addSql('DROP TABLE post_statistic');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF88B5B5C ON post');
        $this->addSql('ALTER TABLE post DROP post_statistic_id');
    }
}
