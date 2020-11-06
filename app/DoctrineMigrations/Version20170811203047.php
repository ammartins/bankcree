<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170811203047 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE transaction_type ADD is_parent TINYINT(1) DEFAULT NULL");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
