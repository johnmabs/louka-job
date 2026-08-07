<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260807085939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, siret VARCHAR(14) DEFAULT NULL, verification_status VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, branding JSON NOT NULL, id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094F989D9B62 ON company (slug)');
        $this->addSql('CREATE TABLE company_member (user_id UUID NOT NULL, role VARCHAR(20) NOT NULL, joined_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, company_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_4D7B9E0D979B1AD6 ON company_member (company_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_company_member_user ON company_member (company_id, user_id)');
        $this->addSql('ALTER TABLE company_member ADD CONSTRAINT FK_4D7B9E0D979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_member DROP CONSTRAINT FK_4D7B9E0D979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_member');
    }
}
