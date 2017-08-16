<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170723181426 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql("CREATE TABLE budget");
        // $this->addSql("ALTER TABLE budget CHANGE goal `limit` INT NOT NULL");
        $this->addSql("CREATE TABLE budget (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, budgetLimit INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE transaction_type DROP FOREIGN KEY FK_6E9D6988727ACA70");
        $this->addSql("DROP INDEX IDX_6E9D6988727ACA70 ON transaction_type");
        $this->addSql("ALTER TABLE transaction_type DROP parent_id");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
