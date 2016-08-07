<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160518225945 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE expense_category TO categories');
        $this->addSql('CREATE TABLE incomes (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, wallet_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, amount INT NOT NULL, currency VARCHAR(64) NOT NULL, INDEX IDX_9DE2B5BD12469DE2 (category_id), INDEX IDX_9DE2B5BD712520F3 (wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE incomes ADD CONSTRAINT FK_9DE2B5BD12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE incomes ADD CONSTRAINT FK_9DE2B5BD712520F3 FOREIGN KEY (wallet_id) REFERENCES wallets (id)');
        $this->addSql('ALTER TABLE bots CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE token token VARCHAR(128) NOT NULL');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE incomes DROP FOREIGN KEY FK_9DE2B5BD12469DE2');
        $this->addSql('RENAME TABLE categories TO expense_category');
        $this->addSql('DROP TABLE incomes');
        $this->addSql('ALTER TABLE bots CHANGE token token VARCHAR(128) DEFAULT \'\' NOT NULL COLLATE utf8_unicode_ci, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
