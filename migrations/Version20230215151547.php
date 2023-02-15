<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215151547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE toy ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE toy ADD CONSTRAINT FK_6705A76EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6705A76EA76ED395 ON toy (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE toy DROP FOREIGN KEY FK_6705A76EA76ED395');
        $this->addSql('DROP INDEX IDX_6705A76EA76ED395 ON toy');
        $this->addSql('ALTER TABLE toy DROP user_id');
    }
}
