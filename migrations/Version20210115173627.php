<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210115173627 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD slug VARCHAR(255) NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD address VARCHAR(255) NOT NULL, ADD city VARCHAR(255) NOT NULL, ADD tel VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP slug');
        $this->addSql('ALTER TABLE `order` DROP slug');
        $this->addSql('ALTER TABLE user DROP slug, DROP first_name, DROP last_name, DROP address, DROP city, DROP tel');
    }
}
