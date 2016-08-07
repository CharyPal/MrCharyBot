<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160508130938 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE expense_category (id INT AUTO_INCREMENT NOT NULL, wallet_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_C02DDB38712520F3 (wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expense_category ADD CONSTRAINT FK_C02DDB38712520F3 FOREIGN KEY (wallet_id) REFERENCES wallets (id)');
        $this->addSql('ALTER TABLE expenses ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35B12469DE2 FOREIGN KEY (category_id) REFERENCES expense_category (id)');
        $this->addSql('CREATE INDEX IDX_2496F35B12469DE2 ON expenses (category_id)');
        $this->addSql('ALTER TABLE message_offset CHANGE updated_at updated_at DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B12469DE2');
        $this->addSql('DROP TABLE expense_category');
        $this->addSql('DROP INDEX IDX_2496F35B12469DE2 ON expenses');
        $this->addSql('ALTER TABLE expenses DROP category_id');
        $this->addSql('ALTER TABLE message_offset CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
