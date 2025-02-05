<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250204171709 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE event (
                id UUID NOT NULL,
                owner_id UUID NOT NULL,
                description TEXT NOT NULL,
                status VARCHAR(255) NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
           )
        SQL);
        $this->addSql(<<<SQL
            CREATE INDEX IDX_3BAE0AA77E3C61F9 ON event (owner_id)
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE event
                ADD CONSTRAINT FK_3BAE0AA77E3C61F9
                    FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
            ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA77E3C61F9
        SQL);
        $this->addSql(<<<SQL
            DROP TABLE event
        SQL);
    }
}
