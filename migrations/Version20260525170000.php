<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260525170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add category table and product category relation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(120) NOT NULL, UNIQUE INDEX UNIQ_CATEGORY_NAME (name), UNIQUE INDEX UNIQ_CATEGORY_SLUG (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE products ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_PRODUCT_CATEGORY FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_PRODUCT_CATEGORY ON products (category_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_PRODUCT_CATEGORY');
        $this->addSql('DROP INDEX IDX_PRODUCT_CATEGORY ON products');
        $this->addSql('ALTER TABLE products DROP category_id');
        $this->addSql('DROP TABLE category');
    }
}
