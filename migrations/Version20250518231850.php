<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518231850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordre CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE ordre_produit DROP FOREIGN KEY FK_ORDREPRODUIT_ORDRE');
        $this->addSql('ALTER TABLE ordre_produit DROP FOREIGN KEY FK_ORDREPRODUIT_PRODUIT');
        $this->addSql('ALTER TABLE ordre_produit ADD prix_unitaire DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE ordre_produit ADD CONSTRAINT FK_D20CF0459291498C FOREIGN KEY (ordre_id) REFERENCES ordre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ordre_produit ADD CONSTRAINT FK_D20CF045F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordre CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE ordre_produit DROP FOREIGN KEY FK_D20CF0459291498C');
        $this->addSql('ALTER TABLE ordre_produit DROP FOREIGN KEY FK_D20CF045F347EFB');
        $this->addSql('ALTER TABLE ordre_produit DROP prix_unitaire');
        $this->addSql('ALTER TABLE ordre_produit ADD CONSTRAINT FK_ORDREPRODUIT_ORDRE FOREIGN KEY (ordre_id) REFERENCES ordre (id)');
        $this->addSql('ALTER TABLE ordre_produit ADD CONSTRAINT FK_ORDREPRODUIT_PRODUIT FOREIGN KEY (produit_id) REFERENCES produit (id)');
    }
}
