<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170602205502 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("CREATE TABLE accounts (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB";
        $this->addSql("ALTER TABLE transaction_type ADD discard TINYINT(1) DEFAULT NULL, CHANGE recurring recurring TINYINT(1) NOT NULL";
        $this->addSql("ALTER TABLE transactions DROP FOREIGN KEY transactions_ibfk_1";
        $this->addSql("DROP INDEX UNIQ_EAA81A4C664F7A0B ON transactions";
        $this->addSql("ALTER TABLE transactions DROP transactions_type";

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
