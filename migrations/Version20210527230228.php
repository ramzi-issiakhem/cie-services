<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210527230228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD name_ar VARCHAR(255) NOT NULL, ADD description_fr LONGTEXT NOT NULL, ADD description_en LONGTEXT NOT NULL, CHANGE description description_ar LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE product ADD name_ar VARCHAR(255) NOT NULL, ADD description_en LONGTEXT NOT NULL, ADD description_fr LONGTEXT NOT NULL, CHANGE description description_ar LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP name_ar, DROP description_ar, DROP description_fr, DROP description_en');
        $this->addSql('ALTER TABLE product ADD description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP name_ar, DROP description_ar, DROP description_en, DROP description_fr');
    }
}
