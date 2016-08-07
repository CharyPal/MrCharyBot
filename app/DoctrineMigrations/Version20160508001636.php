<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160508001636 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tbbc_money_doctrine_storage_ratios (id INT AUTO_INCREMENT NOT NULL, currency_code VARCHAR(3) NOT NULL, ratio DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_1168A609FDA273EC (currency_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbbc_money_ratio_history (id INT AUTO_INCREMENT NOT NULL, currency_code VARCHAR(3) NOT NULL, reference_currency_code VARCHAR(3) NOT NULL, ratio DOUBLE PRECISION NOT NULL, saved_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expenses (id INT AUTO_INCREMENT NOT NULL, wallet_id INT DEFAULT NULL, amount INT NOT NULL, currency VARCHAR(64) NOT NULL, INDEX IDX_2496F35B712520F3 (wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallets (id INT AUTO_INCREMENT NOT NULL, account VARCHAR(64) NOT NULL, default_currency VARCHAR(32) NOT NULL, UNIQUE INDEX account (account), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35B712520F3 FOREIGN KEY (wallet_id) REFERENCES wallets (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B712520F3');
        $this->addSql('DROP TABLE tbbc_money_doctrine_storage_ratios');
        $this->addSql('DROP TABLE tbbc_money_ratio_history');
        $this->addSql('DROP TABLE expenses');
        $this->addSql('DROP TABLE wallets');
    }
}
