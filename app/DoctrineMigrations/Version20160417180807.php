<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
* Auto-generated Migration: Please modify to your needs!
*/
class Version20160417180807 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql("ALTER TABLE transactions ADD transactions_type integer");
        $this->addSql(
            "ALTER TABLE transactions
            ADD FOREIGN KEY (transaction_type)
            REFERENCES transaction_type(id)"
        );
        $this->addSql("ALTER TABLE transaction_type ADD recurring bool");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
