<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231221094047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories ADD technologie_id INT DEFAULT NULL, DROP slug');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668261A27D2 FOREIGN KEY (technologie_id) REFERENCES technologies (id)');
        $this->addSql('CREATE INDEX IDX_3AF34668261A27D2 ON categories (technologie_id)');
        $this->addSql('ALTER TABLE status ADD color VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD avatar_path VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668261A27D2');
        $this->addSql('DROP INDEX IDX_3AF34668261A27D2 ON categories');
        $this->addSql('ALTER TABLE categories ADD slug VARCHAR(255) NOT NULL, DROP technologie_id');
        $this->addSql('ALTER TABLE user DROP avatar_path');
        $this->addSql('ALTER TABLE status DROP color');
    }
}
