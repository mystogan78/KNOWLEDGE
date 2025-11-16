<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251031084245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase ADD user_id INT NOT NULL, DROP ser, CHANGE provider_payment_inten_id provider_payment_intent_id VARCHAR(255) DEFAULT NULL, CHANGE uptade_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6117D13BA76ED395 ON purchase (user_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_purchase_session ON purchase (provider_session_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BA76ED395');
        $this->addSql('DROP INDEX IDX_6117D13BA76ED395 ON purchase');
        $this->addSql('DROP INDEX uniq_purchase_session ON purchase');
        $this->addSql('ALTER TABLE purchase ADD ser VARCHAR(255) NOT NULL, DROP user_id, CHANGE provider_payment_intent_id provider_payment_inten_id VARCHAR(255) DEFAULT NULL, CHANGE updated_at uptade_at DATETIME DEFAULT NULL');
    }
}
