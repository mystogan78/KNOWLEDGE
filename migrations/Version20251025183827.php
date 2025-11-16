<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251025183827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD theme_color VARCHAR(255) DEFAULT NULL, ADD hero_text LONGTEXT DEFAULT NULL, ADD hero_video_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE lesson CHANGE course_id course_id INT NOT NULL, CHANGE video_url video_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F87474F3989D9B62 ON lesson (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP theme_color, DROP hero_text, DROP hero_video_url');
        $this->addSql('DROP INDEX UNIQ_F87474F3989D9B62 ON lesson');
        $this->addSql('ALTER TABLE lesson CHANGE course_id course_id INT DEFAULT NULL, CHANGE video_url video_url VARCHAR(255) NOT NULL');
    }
}
