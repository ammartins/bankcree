<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170810181924 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql("ALTER TABLE transaction_type ADD parent_id INT DEFAULT NULL");
        // $this->addSql("ALTER TABLE transaction_type ADD CONSTRAINT FK_6E9D6988727ACA70 FOREIGN KEY (parent_id) REFERENCES transaction_type (id)");
        // $this->addSql("CREATE UNIQUE INDEX UNIQ_6E9D6988727ACA70 ON transaction_type (parent_id)");
        // $this->addSql("ALTER TABLE transaction_type DROP FOREIGN KEY FK_6E9D6988727ACA70");
        // $this->addSql("DROP INDEX UNIQ_6E9D6988727ACA70 ON transaction_type");
        // $this->addSql("ALTER TABLE transaction_type ADD CONSTRAINT FK_6E9D6988727ACA70 FOREIGN KEY (parent_id) REFERENCES transaction_type (id)");
        // $this->addSql("CREATE UNIQUE INDEX UNIQ_6E9D6988727ACA70 ON transaction_type (parent_id)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
