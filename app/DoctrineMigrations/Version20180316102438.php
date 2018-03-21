<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180316102438 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            "CREATE TABLE imported
                (
                    id INT AUTO_INCREMENT NOT NULL,
                    fileName VARCHAR(255) NOT NULL,
                    account INT NOT NULL,
                    transactions INT NOT NULL,
                    importedAt DATETIME NOT NULL,
                    UNIQUE INDEX UNIQ_903C36B99C39465B (fileName),
                    PRIMARY KEY(id)
                )
                DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci
                ENGINE = InnoDB"
        );
        $this->addSql("ALTER TABLE transactions DROP possible_match;");
        $this->addSql(
            "ALTER TABLE transaction_type
            CHANGE savings savings TINYINT(1)"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
