<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180317220211 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE user ADD bank_account VARCHAR(255) NOT NULL;");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_8D93D64953A23E0A ON user (bank_account);");
        $this->addSql("ALTER TABLE transactions RENAME INDEX fk_eaa81a4c6e9d6988 TO IDX_EAA81A4C6E9D6988;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
