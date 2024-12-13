<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213002320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
{
    // Vérifiez si la colonne existe avant de faire une mise à jour
    $this->addSql("ALTER TABLE client ADD COLUMN IF NOT EXISTS email VARCHAR(255)");

    // Ajoutez des valeurs par défaut si nécessaire
    $this->addSql("UPDATE client SET email = CONCAT('client_', id, '@exemple.com') WHERE email IS NULL");

    // Appliquez la contrainte NOT NULL si nécessaire
    $this->addSql("ALTER TABLE client ALTER COLUMN email SET NOT NULL");
}



    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE client DROP email');
    }
}
