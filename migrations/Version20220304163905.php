<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220304163905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE incidencia ADD usuario_id INT NOT NULL, ADD cliente_id INT NOT NULL');
        $this->addSql('ALTER TABLE incidencia ADD CONSTRAINT FK_C7C6728CDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE incidencia ADD CONSTRAINT FK_C7C6728CDE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('CREATE INDEX IDX_C7C6728CDB38439E ON incidencia (usuario_id)');
        $this->addSql('CREATE INDEX IDX_C7C6728CDE734E51 ON incidencia (cliente_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE incidencia DROP FOREIGN KEY FK_C7C6728CDB38439E');
        $this->addSql('ALTER TABLE incidencia DROP FOREIGN KEY FK_C7C6728CDE734E51');
        $this->addSql('DROP INDEX IDX_C7C6728CDB38439E ON incidencia');
        $this->addSql('DROP INDEX IDX_C7C6728CDE734E51 ON incidencia');
        $this->addSql('ALTER TABLE incidencia DROP usuario_id, DROP cliente_id');
    }
}
