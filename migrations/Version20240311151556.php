<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311151556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_event (user_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_D96CF1FFA76ED395 (user_id), INDEX IDX_D96CF1FF71F7E88B (event_id), PRIMARY KEY(user_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FF71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD creator_id INT NOT NULL, ADD campus_id INT NOT NULL, ADD status_id INT NOT NULL, ADD venue_id INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA761220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA740A73EBA FOREIGN KEY (venue_id) REFERENCES venue (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA761220EA6 ON event (creator_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7AF5D55E1 ON event (campus_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA76BF700BD ON event (status_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA740A73EBA ON event (venue_id)');
        $this->addSql('ALTER TABLE user ADD campus_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AF5D55E1 ON user (campus_id)');
        $this->addSql('ALTER TABLE venue ADD city_id INT NOT NULL');
        $this->addSql('ALTER TABLE venue ADD CONSTRAINT FK_91911B0D8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_91911B0D8BAC62AF ON venue (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FFA76ED395');
        $this->addSql('ALTER TABLE user_event DROP FOREIGN KEY FK_D96CF1FF71F7E88B');
        $this->addSql('DROP TABLE user_event');
        $this->addSql('ALTER TABLE venue DROP FOREIGN KEY FK_91911B0D8BAC62AF');
        $this->addSql('DROP INDEX IDX_91911B0D8BAC62AF ON venue');
        $this->addSql('ALTER TABLE venue DROP city_id');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA761220EA6');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7AF5D55E1');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76BF700BD');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA740A73EBA');
        $this->addSql('DROP INDEX IDX_3BAE0AA761220EA6 ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA7AF5D55E1 ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA76BF700BD ON event');
        $this->addSql('DROP INDEX IDX_3BAE0AA740A73EBA ON event');
        $this->addSql('ALTER TABLE event DROP creator_id, DROP campus_id, DROP status_id, DROP venue_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AF5D55E1');
        $this->addSql('DROP INDEX IDX_8D93D649AF5D55E1 ON user');
        $this->addSql('ALTER TABLE user DROP campus_id');
    }
}
