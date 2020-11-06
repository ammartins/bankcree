<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190311233114 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE accounts');
        $this->addSql('ALTER TABLE user ADD bank VARCHAR(255) NOT NULL, CHANGE is_savings is_savings TINYINT(1) NOT NULL, CHANGE ignore_savings ignore_savings TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE transactions RENAME INDEX fk_eaa81a4c6e9d6988 TO IDX_EAA81A4C6E9D6988');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE accounts (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transactions RENAME INDEX idx_eaa81a4c6e9d6988 TO FK_EAA81A4C6E9D6988');
        $this->addSql('ALTER TABLE user DROP bank, CHANGE is_savings is_savings INT NOT NULL, CHANGE ignore_savings ignore_savings INT NOT NULL');
    }
}
