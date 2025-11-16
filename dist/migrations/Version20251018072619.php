<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251018072619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user table + adjust messenger_messages.delivered_at';
    }

    public function up(Schema $schema): void
    {
        // Sécurité: on s'assure d'être sur MySQL
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', "Migration can only be executed on 'mysql'.");

        // --- CREATE TABLE user (donné par --dump-sql) ---
        $this->addSql('CREATE TABLE `user` (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // --- ALTER messenger_messages.delivered_at (donné par --dump-sql) ---
        $this->addSql("ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'");
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', "Migration can only be executed on 'mysql'.");

        $this->addSql('DROP TABLE `user`');
        // Optionnel : si tu veux vraiment revenir au type précédent (ici on supprime juste le commentaire custom)
        $this->addSql("ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL");
    }
}