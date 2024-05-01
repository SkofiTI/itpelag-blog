<?php

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

return new class
{
    public function up(Schema $schema): void
    {
        $table = $schema->createTable('posts');
        $table->addColumn('id', Types::INTEGER, [
            'unsigned' => true,
            'autoincrement' => true,
        ]);
        $table->addColumn('user_id', Types::INTEGER, ['unsigned' => true]);
        $table->addColumn('title', Types::STRING);
        $table->addColumn('body', Types::TEXT);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, [
            'default' => 'CURRENT_TIMESTAMP',
        ]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['title']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
    }

    public function down(): string
    {
        return 'posts';
    }
};
