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
        $this->addSql(
            "ALTER TABLE transaction_type
            ADD discard TINYINT(1) DEFAULT NULL,
            CHANGE recurring recurring TINYINT(1) NOT NULL"
        );
        $this->addSql(
            "ALTER TABLE transactions DROP FOREIGN KEY transactions_ibfk_1"
        );
        $this->addSql("ALTER TABLE transactions DROP transactions_type");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
