<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170723195927 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql("ALTER TABLE budget DROP FOREIGN KEY FK_73F2F77B5E237E06");
        $this->addSql("DROP INDEX UNIQ_73F2F77B5E237E06 ON budget");
        $this->addSql("ALTER TABLE budget CHANGE name name VARCHAR(255) NOT NULL");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
