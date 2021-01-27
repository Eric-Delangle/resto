<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210123124339 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, entree VARCHAR(255) NOT NULL, plat VARCHAR(255) NOT NULL, fromage VARCHAR(255) NOT NULL, dessert VARCHAR(255) NOT NULL, boisson VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, quantity INT NOT NULL, registered_at DATETIME NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_menu (purchase_id INT NOT NULL, menu_id INT NOT NULL, INDEX IDX_DB6F2F0558FBEB9 (purchase_id), INDEX IDX_DB6F2F0CCD7E912 (menu_id), PRIMARY KEY(purchase_id, menu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, registered_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchase_menu ADD CONSTRAINT FK_DB6F2F0558FBEB9 FOREIGN KEY (purchase_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchase_menu ADD CONSTRAINT FK_DB6F2F0CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_menu DROP FOREIGN KEY FK_DB6F2F0CCD7E912');
        $this->addSql('ALTER TABLE purchase_menu DROP FOREIGN KEY FK_DB6F2F0558FBEB9');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE purchase_menu');
        $this->addSql('DROP TABLE user');
    }
}
