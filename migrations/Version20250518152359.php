<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518152359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordre DROP FOREIGN KEY FK_ORDRE_USER');
        $this->addSql('ALTER TABLE ordre DROP created_at');
        $this->addSql('DROP INDEX fk_ordre_user ON ordre');
        $this->addSql('CREATE INDEX IDX_737992C9A76ED395 ON ordre (user_id)');
        $this->addSql('ALTER TABLE ordre ADD CONSTRAINT FK_ORDRE_USER FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ordre_produit DROP FOREIGN KEY FK_ORDREPRODUIT_PRODUIT');
        $this->addSql('ALTER TABLE ordre_produit DROP FOREIGN KEY FK_ORDREPRODUIT_ORDRE');
        $this->addSql('ALTER TABLE ordre_produit DROP prix, CHANGE quantite quantity INT NOT NULL');
        $this->addSql('DROP INDEX fk_ordreproduit_ordre ON ordre_produit');
        $this->addSql('CREATE INDEX IDX_D20CF0459291498C ON ordre_produit (ordre_id)');
        $this->addSql('DROP INDEX fk_ordreproduit_produit ON ordre_produit');
        $this->addSql('CREATE INDEX IDX_D20CF045F347EFB ON ordre_produit (produit_id)');
        $this->addSql('ALTER TABLE ordre_produit ADD CONSTRAINT FK_ORDREPRODUIT_PRODUIT FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE ordre_produit ADD CONSTRAINT FK_ORDREPRODUIT_ORDRE FOREIGN KEY (ordre_id) REFERENCES ordre (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordre DROP FOREIGN KEY FK_737992C9A76ED395');
        $this->addSql('ALTER TABLE ordre ADD created_at DATETIME NOT NULL');
        $this->addSql('DROP INDEX idx_737992c9a76ed395 ON ordre');
        $this->addSql('CREATE INDEX FK_ORDRE_USER ON ordre (user_id)');
        $this->addSql('ALTER TABLE ordre ADD CONSTRAINT FK_737992C9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ordre_produit DROP FOREIGN KEY FK_D20CF0459291498C');
        $this->addSql('ALTER TABLE ordre_produit DROP FOREIGN KEY FK_D20CF045F347EFB');
        $this->addSql('ALTER TABLE ordre_produit ADD prix DOUBLE PRECISION NOT NULL, CHANGE quantity quantite INT NOT NULL');
        $this->addSql('DROP INDEX idx_d20cf0459291498c ON ordre_produit');
        $this->addSql('CREATE INDEX FK_ORDREPRODUIT_ORDRE ON ordre_produit (ordre_id)');
        $this->addSql('DROP INDEX idx_d20cf045f347efb ON ordre_produit');
        $this->addSql('CREATE INDEX FK_ORDREPRODUIT_PRODUIT ON ordre_produit (produit_id)');
        $this->addSql('ALTER TABLE ordre_produit ADD CONSTRAINT FK_D20CF0459291498C FOREIGN KEY (ordre_id) REFERENCES ordre (id)');
        $this->addSql('ALTER TABLE ordre_produit ADD CONSTRAINT FK_D20CF045F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
    }
}
