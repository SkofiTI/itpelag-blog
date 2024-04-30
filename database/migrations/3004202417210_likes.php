<?php

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

return new class
{
    public function up(Schema $schema): void
    {
        $table = $schema->createTable('likes');
        $table->addColumn('user_id', Types::INTEGER, ['unsigned' => true]);
        $table->addColumn('post_id', Types::INTEGER, ['unsigned' => true]);

        $table->setPrimaryKey(['user_id', 'post_id']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('posts', ['post_id'], ['id'], ['onDelete' => 'CASCADE']);
    }

    public function down(Schema $schema): void
    {
        //
    }
};
