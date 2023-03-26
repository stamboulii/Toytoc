<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321113609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, toy_id INT NOT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_16DB4F89B524FDDC (toy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B524FDDC FOREIGN KEY (toy_id) REFERENCES toy (id)');
        $this->addSql('ALTER TABLE toy DROP picture');
        $this->addSql('ALTER TABLE user ADD picture VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B524FDDC');
        $this->addSql('DROP TABLE picture');
        $this->addSql('ALTER TABLE toy ADD picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP picture');
    }
}
