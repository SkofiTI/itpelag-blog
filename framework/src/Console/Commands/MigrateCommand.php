<?php

namespace Framework\Console\Commands;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Framework\Console\CommandInterface;

class MigrateCommand implements CommandInterface
{
    private string $name = 'migrate';

    private const MIGRATIONS_TABLE = 'migrations';

    public function __construct(
        private Connection $connection,
        private string $migrationsPath
    ) {
    }

    public function execute(array $parameters = []): int
    {
        try {
            $this->connection->setAutoCommit(false);

            $this->connection->beginTransaction();

            $schemaManager = $this->connection->createSchemaManager();

            $this->createMigrationsTable($schemaManager);

            $migrationsFiles = $this->getMigrationFiles();

            if (isset($parameters['rollback'])) {
                $migrationsFiles = array_reverse($migrationsFiles);

                foreach ($migrationsFiles as $migration) {
                    $migrationInstance = require $this->migrationsPath."/$migration";

                    $tableName = $migrationInstance->down();

                    if ($schemaManager->tablesExist($tableName)) {
                        $schemaManager->dropTable($tableName);
                    }

                    $this->deleteMigration($migration);
                }
            } else {
                $appliedMigrations = $this->getAppliedMigrations();

                $migrationsToApply = array_values(array_diff($migrationsFiles, $appliedMigrations));

                foreach ($migrationsToApply as $migration) {
                    $schema = new Schema();

                    $migrationInstance = require $this->migrationsPath."/$migration";

                    $migrationInstance->up($schema);

                    $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

                    $this->connection->executeQuery($sqlArray[0]);

                    $this->addMigration($migration);
                }
            }

            $this->connection->commit();

        } catch (\Throwable $e) {
            $this->connection->rollBack();

            throw $e;
        }

        return 0;
    }

    private function createMigrationsTable(AbstractSchemaManager $schemaManager): void
    {
        if (! $schemaManager->tablesExist(self::MIGRATIONS_TABLE)) {
            $schema = new Schema();

            $table = $schema->createTable(self::MIGRATIONS_TABLE);

            $table->addColumn('id', Types::INTEGER, [
                'unsigned' => true,
                'autoincrement' => true,
            ]);
            $table->addColumn('migration', Types::STRING);
            $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, [
                'default' => 'CURRENT_TIMESTAMP',
            ]);
            $table->setPrimaryKey(['id']);

            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

            $this->connection->executeQuery($sqlArray[0]);

            echo 'Migrations table created'.PHP_EOL;
        }
    }

    private function getAppliedMigrations(): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('migration')
            ->from(self::MIGRATIONS_TABLE)
            ->executeQuery()
            ->fetchFirstColumn();
    }

    private function getMigrationFiles(): array
    {
        $migrationFiles = scandir($this->migrationsPath);

        $filteredFiles = array_filter($migrationFiles, function ($file) {
            return ! in_array($file, ['.', '..']);
        });

        return array_values($filteredFiles);
    }

    private function addMigration(string $migration): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->insert(self::MIGRATIONS_TABLE)
            ->values(['migration' => ':migration'])
            ->setParameter('migration', $migration)
            ->executeQuery();

        echo "Migration $migration success created".PHP_EOL;
    }

    private function deleteMigration(string $migration): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder->delete(self::MIGRATIONS_TABLE)
            ->where('migration = :migration')
            ->setParameter('migration', $migration)
            ->executeQuery()
            ->rowCount();

        if ($result === 1) {
            $this->connection->executeQuery('ALTER TABLE '.self::MIGRATIONS_TABLE.' AUTO_INCREMENT = 1');

            echo "Migration $migration success deleted".PHP_EOL;
        }
    }
}
