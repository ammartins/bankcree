<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170528134921 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            "ALTER TABLE transactions
            ADD possible_match INT DEFAULT NULL"
        );
        $this->addSql(
            "CREATE UNIQUE INDEX UNIQ_EAA81A4C664F7A0B
            ON transactions (possible_match)"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
