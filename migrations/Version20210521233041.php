<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210521233041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, school_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, reservations INT NOT NULL, description LONGTEXT NOT NULL, reservation_places INT NOT NULL, state INT NOT NULL, event_datetime DATETIME NOT NULL, deadline_date DATE NOT NULL, INDEX IDX_3BAE0AA74584665A (product_id), INDEX IDX_3BAE0AA7C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, logo VARCHAR(255) NOT NULL, cover_image VARCHAR(255) NOT NULL, images VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school (id INT AUTO_INCREMENT NOT NULL, adress VARCHAR(255) NOT NULL, locality VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, mobile_phone VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F99EDABBE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, related_school_id INT NOT NULL, adress VARCHAR(255) NOT NULL, locality VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, mobile_phone VARCHAR(255) NOT NULL, birthday_date DATE DEFAULT NULL, scholar_level INT DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6496BEB1BF5 (related_school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496BEB1BF5 FOREIGN KEY (related_school_id) REFERENCES school (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA74584665A');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7C32A47EE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496BEB1BF5');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE user');
    }
}
