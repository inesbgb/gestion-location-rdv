<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212125550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_admin ADD carousel_image4_id INT DEFAULT NULL, ADD carousel_image5 INT NOT NULL, ADD carousel_image6 INT NOT NULL, DROP carousel_image4');
        $this->addSql('ALTER TABLE element_admin ADD CONSTRAINT FK_4C2C79EF2F3228B9 FOREIGN KEY (carousel_image4_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_4C2C79EF2F3228B9 ON element_admin (carousel_image4_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_admin DROP FOREIGN KEY FK_4C2C79EF2F3228B9');
        $this->addSql('DROP INDEX IDX_4C2C79EF2F3228B9 ON element_admin');
        $this->addSql('ALTER TABLE element_admin ADD carousel_image4 VARCHAR(255) NOT NULL, DROP carousel_image4_id, DROP carousel_image5, DROP carousel_image6');
    }
}
