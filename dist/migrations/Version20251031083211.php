<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251031083211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, lesson_id INT DEFAULT NULL, ser VARCHAR(255) NOT NULL, amount NUMERIC(8, 2) NOT NULL, currency VARCHAR(10) NOT NULL, status VARCHAR(20) NOT NULL, provider VARCHAR(20) NOT NULL, provider_session_id VARCHAR(255) NOT NULL, provider_payment_inten_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', uptade_at DATETIME DEFAULT NULL, INDEX IDX_6117D13B591CC992 (course_id), INDEX IDX_6117D13BCDF80196 (lesson_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE lesson CHANGE content content LONGTEXT DEFAULT NULL, CHANGE position position INT DEFAULT 1 NOT NULL');
        $this->addSql('CREATE INDEX idx_lesson_course_position ON lesson (course_id, position)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B591CC992');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BCDF80196');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP INDEX idx_lesson_course_position ON lesson');
        $this->addSql('ALTER TABLE lesson CHANGE content content LONGTEXT NOT NULL, CHANGE position position INT NOT NULL');
    }
}
