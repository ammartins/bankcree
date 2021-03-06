<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170706105824 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE transaction_type ADD parent_id INT DEFAULT NULL;");
        $this->addSql("CREATE INDEX IDX_6E9D6988727ACA70 ON transaction_type (parent_id)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
