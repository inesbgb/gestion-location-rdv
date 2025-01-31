<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212130343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_admin ADD carousel_image5_id INT DEFAULT NULL, ADD carousel_image6_id INT DEFAULT NULL, DROP carousel_image5, DROP carousel_image6');
        $this->addSql('ALTER TABLE element_admin ADD CONSTRAINT FK_4C2C79EF978E4FDC FOREIGN KEY (carousel_image5_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE element_admin ADD CONSTRAINT FK_4C2C79EF853BE032 FOREIGN KEY (carousel_image6_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_4C2C79EF978E4FDC ON element_admin (carousel_image5_id)');
        $this->addSql('CREATE INDEX IDX_4C2C79EF853BE032 ON element_admin (carousel_image6_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_admin DROP FOREIGN KEY FK_4C2C79EF978E4FDC');
        $this->addSql('ALTER TABLE element_admin DROP FOREIGN KEY FK_4C2C79EF853BE032');
        $this->addSql('DROP INDEX IDX_4C2C79EF978E4FDC ON element_admin');
        $this->addSql('DROP INDEX IDX_4C2C79EF853BE032 ON element_admin');
        $this->addSql('ALTER TABLE element_admin ADD carousel_image5 INT NOT NULL, ADD carousel_image6 INT NOT NULL, DROP carousel_image5_id, DROP carousel_image6_id');
    }
}
