<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260306160958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE author (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, bio CLOB DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDAFD8C8E7927C74 ON author (email)');
        $this->addSql('CREATE TABLE author_audit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs CLOB DEFAULT NULL, extra_data CLOB DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX type_0810b5d0a1bf6d6bba5b1b7c405525bb_idx ON author_audit (type)');
        $this->addSql('CREATE INDEX object_id_0810b5d0a1bf6d6bba5b1b7c405525bb_idx ON author_audit (object_id)');
        $this->addSql('CREATE INDEX discriminator_0810b5d0a1bf6d6bba5b1b7c405525bb_idx ON author_audit (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_0810b5d0a1bf6d6bba5b1b7c405525bb_idx ON author_audit (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_0810b5d0a1bf6d6bba5b1b7c405525bb_idx ON author_audit (blame_id)');
        $this->addSql('CREATE INDEX created_at_0810b5d0a1bf6d6bba5b1b7c405525bb_idx ON author_audit (created_at)');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, author_name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, post_id INTEGER NOT NULL, CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('CREATE TABLE comment_audit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs CLOB DEFAULT NULL, extra_data CLOB DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX type_ce24fca4d7b92a86c970e01d2edf470d_idx ON comment_audit (type)');
        $this->addSql('CREATE INDEX object_id_ce24fca4d7b92a86c970e01d2edf470d_idx ON comment_audit (object_id)');
        $this->addSql('CREATE INDEX discriminator_ce24fca4d7b92a86c970e01d2edf470d_idx ON comment_audit (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_ce24fca4d7b92a86c970e01d2edf470d_idx ON comment_audit (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_ce24fca4d7b92a86c970e01d2edf470d_idx ON comment_audit (blame_id)');
        $this->addSql('CREATE INDEX created_at_ce24fca4d7b92a86c970e01d2edf470d_idx ON comment_audit (created_at)');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, body CLOB NOT NULL, excerpt VARCHAR(500) DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, author_id INTEGER DEFAULT NULL, coauthor_id INTEGER DEFAULT NULL, CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES author (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A8A6C8D9221F99D FOREIGN KEY (coauthor_id) REFERENCES author (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D9221F99D ON post (coauthor_id)');
        $this->addSql('CREATE TABLE post_tag (post_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY (post_id, tag_id), CONSTRAINT FK_5ACE3AF04B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5ACE3AF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5ACE3AF04B89032C ON post_tag (post_id)');
        $this->addSql('CREATE INDEX IDX_5ACE3AF0BAD26311 ON post_tag (tag_id)');
        $this->addSql('CREATE TABLE post_audit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs CLOB DEFAULT NULL, extra_data CLOB DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX type_7d2ab6760afca296cbe1bbe3d5f25777_idx ON post_audit (type)');
        $this->addSql('CREATE INDEX object_id_7d2ab6760afca296cbe1bbe3d5f25777_idx ON post_audit (object_id)');
        $this->addSql('CREATE INDEX discriminator_7d2ab6760afca296cbe1bbe3d5f25777_idx ON post_audit (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_7d2ab6760afca296cbe1bbe3d5f25777_idx ON post_audit (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_7d2ab6760afca296cbe1bbe3d5f25777_idx ON post_audit (blame_id)');
        $this->addSql('CREATE INDEX created_at_7d2ab6760afca296cbe1bbe3d5f25777_idx ON post_audit (created_at)');
        $this->addSql('CREATE TABLE tag (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL, color VARCHAR(7) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B7835E237E06 ON tag (name)');
        $this->addSql('CREATE TABLE tag_audit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs CLOB DEFAULT NULL, extra_data CLOB DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX type_393c773bd22d1a1a39721c0537b3631b_idx ON tag_audit (type)');
        $this->addSql('CREATE INDEX object_id_393c773bd22d1a1a39721c0537b3631b_idx ON tag_audit (object_id)');
        $this->addSql('CREATE INDEX discriminator_393c773bd22d1a1a39721c0537b3631b_idx ON tag_audit (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_393c773bd22d1a1a39721c0537b3631b_idx ON tag_audit (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_393c773bd22d1a1a39721c0537b3631b_idx ON tag_audit (blame_id)');
        $this->addSql('CREATE INDEX created_at_393c773bd22d1a1a39721c0537b3631b_idx ON tag_audit (created_at)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE author_audit');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE comment_audit');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_tag');
        $this->addSql('DROP TABLE post_audit');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_audit');
        $this->addSql('DROP TABLE "user"');
    }
}
