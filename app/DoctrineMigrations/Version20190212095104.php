<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190212095104 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(file_get_contents(__DIR__ . '/initial.sql'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
