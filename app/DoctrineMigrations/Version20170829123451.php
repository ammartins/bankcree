<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170829123451 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql("DROP INDEX IDX_EAA81A4C664F7A0B ON transactions");
        $this->addSql(
            "ALTER TABLE transactions
            ADD match_percentage INT DEFAULT NULL,
            CHANGE description description LONGTEXT DEFAULT NULL"
        );
        $this->addSql(
            "CREATE TABLE accounts (
                id INT AUTO_INCREMENT NOT NULL,
                parent_id INT NOT NULL,
                user_id INT NOT NULL,
                PRIMARY KEY(id)
            )
                DEFAULT CHARACTER
                SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB"
        );
        $this->addSql(
            "CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)"
        );
        $this->addSql(
            "ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4C664F7A0B"
        );
        $this->addSql(
            "DROP INDEX UNIQ_EAA81A4C664F7A0B ON transactions"
        );
        $this->addSql(
            "ALTER TABLE transactions
            ADD CONSTRAINT FK_EAA81A4C6E9D6988 FOREIGN KEY (transaction_type)
            REFERENCES transaction_type (id)"
        );
        $this->addSql(
            "ALTER TABLE transaction_type ADD parent_id INT DEFAULT NULL"
        );
        $this->addSql(
            "ALTER TABLE transaction_type
            ADD CONSTRAINT FK_6E9D6988727ACA70 FOREIGN KEY (parent_id)
            REFERENCES transaction_type (id)"
        );
        $this->addSql(
            "CREATE INDEX IDX_6E9D6988727ACA70 ON transaction_type (parent_id)"
        );
        $this->addSql(
            "ALTER TABLE transactions
            RENAME INDEX fk_eaa81a4c6e9d6988e TO IDX_EAA81A4C6E9D6988"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
