<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531134056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE child (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, school_id INT NOT NULL, name VARCHAR(255) NOT NULL, schoolar_level INT NOT NULL, birthday_date DATE NOT NULL, INDEX IDX_22B35429727ACA70 (parent_id), INDEX IDX_22B35429C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, school_id INT NOT NULL, name VARCHAR(255) NOT NULL, name_ar VARCHAR(255) NOT NULL, price INT NOT NULL, description_ar LONGTEXT NOT NULL, description_fr LONGTEXT NOT NULL, description_en LONGTEXT NOT NULL, reservation_places INT NOT NULL, state INT NOT NULL, event_datetime DATETIME NOT NULL, deadline_date DATE NOT NULL, reservations LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_3BAE0AA74584665A (product_id), INDEX IDX_3BAE0AA7C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, name_ar VARCHAR(255) NOT NULL, description_ar LONGTEXT NOT NULL, description_en LONGTEXT NOT NULL, description_fr LONGTEXT NOT NULL, logo VARCHAR(255) NOT NULL, cover_image VARCHAR(255) NOT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, adress VARCHAR(255) NOT NULL, locality VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, mobile_phone VARCHAR(255) NOT NULL, birthday_date DATE DEFAULT NULL, name VARCHAR(255) NOT NULL, type INT NOT NULL, logo VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B35429727ACA70 FOREIGN KEY (parent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B35429C32A47EE FOREIGN KEY (school_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7C32A47EE FOREIGN KEY (school_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA74584665A');
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B35429727ACA70');
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B35429C32A47EE');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7C32A47EE');
        $this->addSql('DROP TABLE child');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE user');
    }
}
