<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200713090217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE block_list DROP FOREIGN KEY FK_82A6AA639D86650F');
        $this->addSql('ALTER TABLE block_list DROP FOREIGN KEY FK_82A6AA63BEF137EE');
        $this->addSql('DROP INDEX IDX_82A6AA639D86650F ON block_list');
        $this->addSql('DROP INDEX IDX_82A6AA63BEF137EE ON block_list');
        $this->addSql('ALTER TABLE block_list ADD user_id INT DEFAULT NULL, ADD participant_id INT DEFAULT NULL, DROP user_id_id, DROP participant_id_id');
        $this->addSql('ALTER TABLE block_list ADD CONSTRAINT FK_82A6AA63A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE block_list ADD CONSTRAINT FK_82A6AA639D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
        $this->addSql('CREATE INDEX IDX_82A6AA63A76ED395 ON block_list (user_id)');
        $this->addSql('CREATE INDEX IDX_82A6AA639D1C3019 ON block_list (participant_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE block_list DROP FOREIGN KEY FK_82A6AA63A76ED395');
        $this->addSql('ALTER TABLE block_list DROP FOREIGN KEY FK_82A6AA639D1C3019');
        $this->addSql('DROP INDEX IDX_82A6AA63A76ED395 ON block_list');
        $this->addSql('DROP INDEX IDX_82A6AA639D1C3019 ON block_list');
        $this->addSql('ALTER TABLE block_list ADD user_id_id INT DEFAULT NULL, ADD participant_id_id INT DEFAULT NULL, DROP user_id, DROP participant_id');
        $this->addSql('ALTER TABLE block_list ADD CONSTRAINT FK_82A6AA639D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE block_list ADD CONSTRAINT FK_82A6AA63BEF137EE FOREIGN KEY (participant_id_id) REFERENCES participant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_82A6AA639D86650F ON block_list (user_id_id)');
        $this->addSql('CREATE INDEX IDX_82A6AA63BEF137EE ON block_list (participant_id_id)');
    }
}
