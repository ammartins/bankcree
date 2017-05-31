<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170528142111 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE transactions ADD possible_match INT DEFAULT NULL");
        $this->addSql("ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C664F7A0B FOREIGN KEY (possible_match) REFERENCES transactions (id)");
        $this->addSql("CREATE INDEX IDX_EAA81A4C664F7A0B ON transactions (possible_match)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
