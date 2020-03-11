<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200311105104 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE composition (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD composition_id INT NOT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2787A2E12 FOREIGN KEY (composition_id) REFERENCES composition (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC2787A2E12 ON produit (composition_id)');
        $this->addSql('ALTER TABLE recette ADD composition_id INT NOT NULL');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT FK_49BB639087A2E12 FOREIGN KEY (composition_id) REFERENCES composition (id)');
        $this->addSql('CREATE INDEX IDX_49BB639087A2E12 ON recette (composition_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2787A2E12');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY FK_49BB639087A2E12');
        $this->addSql('DROP TABLE composition');
        $this->addSql('DROP INDEX IDX_29A5EC2787A2E12 ON produit');
        $this->addSql('ALTER TABLE produit DROP composition_id, CHANGE photo photo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX IDX_49BB639087A2E12 ON recette');
        $this->addSql('ALTER TABLE recette DROP composition_id');
    }
}
