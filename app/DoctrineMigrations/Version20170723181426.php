<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170723181426 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql("DROP INDEX IDX_6E9D6988727ACA70 ON transaction_type");
        $this->addSql("ALTER TABLE transaction_type DROP parent_id");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
