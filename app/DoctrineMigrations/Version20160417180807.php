<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Schema\Schema;

/**
* Auto-generated Migration: Please modify to your needs!
*/
class Version20160417180807 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            "CREATE TABLE transactions (
                id INT(11) NOT NULL AUTO_INCREMENT,
                transaction_type INT(11),
                transaction_hash varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                create_at datetime NOT NULL,
                startsaldo double NOT NULL,
                endsaldo double NOT NULL,
                amount double NOT NULL,
                description longtext COLLATE utf8_unicode_ci,
                short_description varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                account_id int(11) NOT NULL,
                PRIMARY KEY (`id`)
            )"
        );

        $this->addSql(
            "CREATE TABLE transaction_type (
                id INT(11) NOT NULL AUTO_INCREMENT,
                account_id int(11) NOT NULL,
                name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                PRIMARY KEY (`id`)
            )"
        );

        $this->addSql(
            "CREATE TABLE user (
                id INT(11) NOT NULL AUTO_INCREMENT,
                username varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                email varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                password varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                created_at datetime NOT NULL,
                is_active tinyint(1) NOT NULL,
                PRIMARY KEY (`id`)
            )"
        );

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
    public function down(Schema $schema)
    {
    }
}
