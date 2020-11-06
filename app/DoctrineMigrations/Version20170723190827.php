<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170723190827 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up((Schema $schema) : void
    {
        $this->addSql("ALTER TABLE budget DROP FOREIGN KEY FK_73F2F77B71179CD6");
        $this->addSql("DROP INDEX UNIQ_73F2F77B71179CD6 ON budget");
        $this->addSql("ALTER TABLE budget CHANGE name_id name INT DEFAULT NULL");
        $this->addSql("ALTER TABLE budget ADD CONSTRAINT FK_73F2F77B5E237E06 FOREIGN KEY (name) REFERENCES transaction_type (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_73F2F77B5E237E06 ON budget (name)");
    }

    /**
     * @param Schema $schema
     */
    public function down((Schema $schema) : void
    {
    }
}
