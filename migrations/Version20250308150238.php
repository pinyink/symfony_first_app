<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308150238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF88B5B5C');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF88B5B5C ON post');
        $this->addSql('ALTER TABLE post DROP post_statistic_id');
        $this->addSql('ALTER TABLE post_statistic ADD post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post_statistic ADD CONSTRAINT FK_C5F5386B4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_C5F5386B4B89032C ON post_statistic (post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD post_statistic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF88B5B5C FOREIGN KEY (post_statistic_id) REFERENCES post_statistic (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF88B5B5C ON post (post_statistic_id)');
        $this->addSql('ALTER TABLE post_statistic DROP FOREIGN KEY FK_C5F5386B4B89032C');
        $this->addSql('DROP INDEX IDX_C5F5386B4B89032C ON post_statistic');
        $this->addSql('ALTER TABLE post_statistic DROP post_id');
    }
}
