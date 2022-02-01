<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220201090419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE catalog (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, function VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, mobile_phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE child (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, school_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, schoolar_level INT NOT NULL, birthday_date DATE NOT NULL, events LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_22B35429727ACA70 (parent_id), INDEX IDX_22B35429C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, school_id INT NOT NULL, name VARCHAR(255) NOT NULL, name_ar VARCHAR(255) NOT NULL, price INT NOT NULL, description_ar LONGTEXT NOT NULL, description_fr LONGTEXT NOT NULL, description_en LONGTEXT NOT NULL, reservation_places INT NOT NULL, state INT NOT NULL, event_datetime DATETIME NOT NULL, deadline_date DATE NOT NULL, reservations LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_3BAE0AA74584665A (product_id), INDEX IDX_3BAE0AA7C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, name_ar VARCHAR(255) NOT NULL, description_ar LONGTEXT NOT NULL, description_en LONGTEXT NOT NULL, description_fr LONGTEXT NOT NULL, logo VARCHAR(255) NOT NULL, cover_image VARCHAR(255) NOT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, adress VARCHAR(255) NOT NULL, locality VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, mobile_phone VARCHAR(255) NOT NULL, birthday_date DATE DEFAULT NULL, name VARCHAR(255) NOT NULL, facebook_id VARCHAR(255) DEFAULT NULL, facebook_token VARCHAR(255) DEFAULT NULL, type INT NOT NULL, logo VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B35429727ACA70 FOREIGN KEY (parent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B35429C32A47EE FOREIGN KEY (school_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7C32A47EE FOREIGN KEY (school_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA74584665A');
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B35429727ACA70');
        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B35429C32A47EE');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7C32A47EE');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE catalog');
        $this->addSql('DROP TABLE child');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
    }
}
