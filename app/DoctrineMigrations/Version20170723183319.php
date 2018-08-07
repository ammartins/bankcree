<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170723183319 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE budget ADD name_id INT DEFAULT NULL, DROP name");
        $this->addSql("ALTER TABLE budget ADD CONSTRAINT FK_73F2F77B71179CD6 FOREIGN KEY (name_id) REFERENCES transaction_type (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_73F2F77B71179CD6 ON budget (name_id)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
