#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

class MigrationRunner {
    private $connection;
    private $migrationPath;

    public function __construct() {
        // Database connection parameters
        $host = 'database.cc.localhost';
        $dbname = 'course_catalog';
        $username = 'test_user';
        $password = 'test_password';

        // Migration path
        $this->migrationPath = dirname(__DIR__) . '/database/migrations/';

        try {
            // Create PDO connection
            $this->connection = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $username,
                $password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->error("Database connection failed: " . $e->getMessage());
            exit(1);
        }
    }

    /**
     * Run all migrations
     */
    public function runAllMigrations() {
        // Get all migration files, sorted by filename (which should be timestamp-prefixed)
        $migrationFiles = glob($this->migrationPath . '*_migration.sql');
        sort($migrationFiles);

        foreach ($migrationFiles as $file) {
            $this->runMigration($file);
        }
    }

    /**
     * Run a specific migration file
     */
    public function runMigration($filePath) {
        if (!file_exists($filePath)) {
            $this->error("Migration file not found: $filePath");
            return false;
        }

        // Read migration file content
        $migrationContent = file_get_contents($filePath);

        // Split migration into up and down sections
        preg_match('/-- up\n(.*?)\n-- down\n(.*)/s', $migrationContent, $matches);

        if (count($matches) !== 3) {
            $this->error("Invalid migration file format: $filePath");
            return false;
        }

        $upSection = trim($matches[1]);
        $downSection = trim($matches[2]);

        try {
            // Execute UP section (drop existing table if exists, then create)
            $this->executeStatements($downSection);
            $this->executeStatements($upSection);

            $this->info("Applied migration: " . basename($filePath));
            return true;
        } catch (PDOException $e) {
            $this->error("Migration failed: " . $e->getMessage());

            return false;
        }
    }

    /**
     * Execute multiple SQL statements
     */
    private function executeStatements($statements) {
        // Split statements by semicolon
        $statements = array_filter(array_map('trim', explode(';', $statements)));

        foreach ($statements as $statement) {
            if (empty($statement)) continue;

            $stmt = $this->connection->prepare($statement);
            $stmt->execute();
        }
    }

    /**
     * Output info message
     */
    private function info($message) {
        echo "\033[32m[INFO]\033[0m $message\n";
    }

    /**
     * Output error message
     */
    private function error($message) {
        echo "\033[31m[ERROR]\033[0m $message\n";
    }

    /**
     * CLI entry point
     */
    public static function run($argv) {
        $runner = new self();

        // Remove script name from arguments
        array_shift($argv);

        // Default to running all migrations
        if (empty($argv)) {
            $runner->runAllMigrations();
            return;
        }

        // Process specific commands
        $command = $argv[0];
        switch ($command) {
            case 'all':
                $runner->runAllMigrations();
                break;
            case 'list':
                $files = glob($runner->migrationPath . '*_migration.sql');
                sort($files);
                echo "Available migrations:\n";
                foreach ($files as $file) {
                    echo " - " . basename($file) . "\n";
                }
                break;
            default:
                // Assume it's a specific migration file
                $migrationFile = $runner->migrationPath . $command;
                if (file_exists($migrationFile)) {
                    $runner->runMigration($migrationFile);
                } else {
                    echo "Usage: php migrate_runner.php [all|list|migration_filename]\n";
                    exit(1);
                }
        }
    }
}

// Run the migration runner
MigrationRunner::run($argv);