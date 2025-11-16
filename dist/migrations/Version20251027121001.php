<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251027121001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ intro_text à la table course';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $table = $sm->introspectTable('course');

        if (!$table->hasColumn('intro_text')) {
            $this->addSql('ALTER TABLE course ADD intro_text LONGTEXT DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $table = $sm->introspectTable('course');

        if ($table->hasColumn('intro_text')) {
            $this->addSql('ALTER TABLE course DROP intro_text');
        }
    }
}
