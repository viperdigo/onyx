<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150903102731 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_log (id INT AUTO_INCREMENT NOT NULL, fk_user INT DEFAULT NULL, entity VARCHAR(255) NOT NULL, entity_id VARCHAR(255) NOT NULL, `values` LONGTEXT NOT NULL, user_ip VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_F6E1C0F51AD0877 (fk_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address TINYTEXT DEFAULT NULL, zipcode VARCHAR(8) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, balance NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, status ENUM(\'activated\', \'blocked\'), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_balance_logs (id INT AUTO_INCREMENT NOT NULL, fk_customer INT DEFAULT NULL, fk_user INT DEFAULT NULL, controller VARCHAR(500) NOT NULL, balance NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_EC73F442B311CDD7 (fk_customer), INDEX IDX_EC73F4421AD0877 (fk_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_status_log (id INT AUTO_INCREMENT NOT NULL, fk_customer INT DEFAULT NULL, created_at DATETIME NOT NULL, fk_user INT DEFAULT NULL, status ENUM(\'activated\', \'blocked\'), INDEX IDX_AC993CE5B311CDD7 (fk_customer), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintenance_products (id INT AUTO_INCREMENT NOT NULL, fk_customer INT NOT NULL, fk_product INT NOT NULL, quantity INT NOT NULL, status ENUM(\'entered\', \'sent\',\'received\',\'returned\',\'canceled\'), note TINYTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_ADC25E77B311CDD7 (fk_customer), INDEX IDX_ADC25E7723653981 (fk_product), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintenance_status_log (id INT AUTO_INCREMENT NOT NULL, fk_maintenance INT DEFAULT NULL, created_at DATETIME NOT NULL, fk_user INT DEFAULT NULL, status ENUM(\'entered\', \'sent\',\'received\',\'returned\',\'canceled\'), INDEX IDX_7617B8E9467254DD (fk_maintenance), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, fk_customer INT DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, note TINYTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E52FFDEEB311CDD7 (fk_customer), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, fk_order INT DEFAULT NULL, fk_product INT DEFAULT NULL, sold_value NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, subtotal NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_62809DB034C4B0ED (fk_order), INDEX IDX_62809DB023653981 (fk_product), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payments (id INT AUTO_INCREMENT NOT NULL, fk_customer INT DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, note TINYTEXT DEFAULT NULL, received_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_65D29B32B311CDD7 (fk_customer), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, fk_supplier INT DEFAULT NULL, description VARCHAR(255) NOT NULL, cost_value NUMERIC(10, 2) NOT NULL, sale_value NUMERIC(10, 2) NOT NULL, storage INT DEFAULT NULL, type ENUM(\'simple\', \'cabinet\', \'compound\' ), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B3BA5A5AA9022FA0 (fk_supplier), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_components (id INT AUTO_INCREMENT NOT NULL, fk_product INT DEFAULT NULL, fk_component INT DEFAULT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3C170DE323653981 (fk_product), INDEX IDX_3C170DE328A776C7 (fk_component), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_logs (id INT AUTO_INCREMENT NOT NULL, fk_product INT DEFAULT NULL, fk_user INT DEFAULT NULL, controller VARCHAR(300) NOT NULL, amount INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_F9A20D4923653981 (fk_product), INDEX IDX_F9A20D491AD0877 (fk_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suppliers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, document VARCHAR(14) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, created_id INT DEFAULT NULL, updated_id INT DEFAULT NULL, user_type_id INT DEFAULT NULL, username VARCHAR(20) NOT NULL, name VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', isActive TINYINT(1) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, plainPassword VARCHAR(255) DEFAULT NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E9989D9B62 (slug), INDEX IDX_1483A5E95EE01E44 (created_id), INDEX IDX_1483A5E9960CC7F3 (updated_id), INDEX IDX_1483A5E99D419299 (user_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_types (id INT AUTO_INCREMENT NOT NULL, created_id INT DEFAULT NULL, updated_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BBF272685EE01E44 (created_id), INDEX IDX_BBF27268960CC7F3 (updated_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_log ADD CONSTRAINT FK_F6E1C0F51AD0877 FOREIGN KEY (fk_user) REFERENCES users (id)');
        $this->addSql('ALTER TABLE customer_balance_logs ADD CONSTRAINT FK_EC73F442B311CDD7 FOREIGN KEY (fk_customer) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE customer_balance_logs ADD CONSTRAINT FK_EC73F4421AD0877 FOREIGN KEY (fk_user) REFERENCES users (id)');
        $this->addSql('ALTER TABLE customer_status_log ADD CONSTRAINT FK_AC993CE5B311CDD7 FOREIGN KEY (fk_customer) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE maintenance_products ADD CONSTRAINT FK_ADC25E77B311CDD7 FOREIGN KEY (fk_customer) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE maintenance_products ADD CONSTRAINT FK_ADC25E7723653981 FOREIGN KEY (fk_product) REFERENCES products (id)');
        $this->addSql('ALTER TABLE maintenance_status_log ADD CONSTRAINT FK_7617B8E9467254DD FOREIGN KEY (fk_maintenance) REFERENCES maintenance_products (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEB311CDD7 FOREIGN KEY (fk_customer) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB034C4B0ED FOREIGN KEY (fk_order) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB023653981 FOREIGN KEY (fk_product) REFERENCES products (id)');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32B311CDD7 FOREIGN KEY (fk_customer) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AA9022FA0 FOREIGN KEY (fk_supplier) REFERENCES suppliers (id)');
        $this->addSql('ALTER TABLE products_components ADD CONSTRAINT FK_3C170DE323653981 FOREIGN KEY (fk_product) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_components ADD CONSTRAINT FK_3C170DE328A776C7 FOREIGN KEY (fk_component) REFERENCES products (id)');
        $this->addSql('ALTER TABLE storage_logs ADD CONSTRAINT FK_F9A20D4923653981 FOREIGN KEY (fk_product) REFERENCES products (id)');
        $this->addSql('ALTER TABLE storage_logs ADD CONSTRAINT FK_F9A20D491AD0877 FOREIGN KEY (fk_user) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E95EE01E44 FOREIGN KEY (created_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9960CC7F3 FOREIGN KEY (updated_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E99D419299 FOREIGN KEY (user_type_id) REFERENCES user_types (id)');
        $this->addSql('ALTER TABLE user_types ADD CONSTRAINT FK_BBF272685EE01E44 FOREIGN KEY (created_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_types ADD CONSTRAINT FK_BBF27268960CC7F3 FOREIGN KEY (updated_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_balance_logs DROP FOREIGN KEY FK_EC73F442B311CDD7');
        $this->addSql('ALTER TABLE customer_status_log DROP FOREIGN KEY FK_AC993CE5B311CDD7');
        $this->addSql('ALTER TABLE maintenance_products DROP FOREIGN KEY FK_ADC25E77B311CDD7');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEB311CDD7');
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B32B311CDD7');
        $this->addSql('ALTER TABLE maintenance_status_log DROP FOREIGN KEY FK_7617B8E9467254DD');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB034C4B0ED');
        $this->addSql('ALTER TABLE maintenance_products DROP FOREIGN KEY FK_ADC25E7723653981');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB023653981');
        $this->addSql('ALTER TABLE products_components DROP FOREIGN KEY FK_3C170DE323653981');
        $this->addSql('ALTER TABLE products_components DROP FOREIGN KEY FK_3C170DE328A776C7');
        $this->addSql('ALTER TABLE storage_logs DROP FOREIGN KEY FK_F9A20D4923653981');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AA9022FA0');
        $this->addSql('ALTER TABLE audit_log DROP FOREIGN KEY FK_F6E1C0F51AD0877');
        $this->addSql('ALTER TABLE customer_balance_logs DROP FOREIGN KEY FK_EC73F4421AD0877');
        $this->addSql('ALTER TABLE storage_logs DROP FOREIGN KEY FK_F9A20D491AD0877');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E95EE01E44');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9960CC7F3');
        $this->addSql('ALTER TABLE user_types DROP FOREIGN KEY FK_BBF272685EE01E44');
        $this->addSql('ALTER TABLE user_types DROP FOREIGN KEY FK_BBF27268960CC7F3');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E99D419299');
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE customer_balance_logs');
        $this->addSql('DROP TABLE customer_status_log');
        $this->addSql('DROP TABLE maintenance_products');
        $this->addSql('DROP TABLE maintenance_status_log');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE products_components');
        $this->addSql('DROP TABLE storage_logs');
        $this->addSql('DROP TABLE suppliers');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_types');
    }
}
