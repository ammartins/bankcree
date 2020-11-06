<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170828213959 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up((Schema $schema) : void
    {
        $this->addSql("ALTER TABLE transactions CHANGE possible_match possible_match INT DEFAULT NULL");
    }

    /**
     * @param Schema $schema
     */
    public function down((Schema $schema) : void
    {
    }
}
