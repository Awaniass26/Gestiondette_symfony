<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218155508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_roles_jsonb');
        $this->addSql('ALTER TABLE "user" ALTER adresse TYPE VARCHAR(39)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649450FF010 ON "user" (telephone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_8D93D649450FF0103');
        $this->addSql('ALTER TABLE "user" ALTER adresse TYPE VARCHAR(255)');
        $this->addSql('CREATE INDEX idx_roles_jsonb ON "user" (roles)');
    }
}
