#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use CourseCatalog\Helpers\DatabaseMigrationHelper;

class MigrationCLI {
    private $helper;

    public function __construct() {
        $this->helper = new DatabaseMigrationHelper();
    }

    public function run($argv) {
        // Remove script name from arguments
        array_shift($argv);

        // Default action if no arguments
        if (empty($argv)) {
            $this->generateAllMigrations();
            return;
        }

        // Process specific commands
        $command = $argv[0];
        switch ($command) {
            case 'categories':
                $this->helper->generateCategoriesMigration();
                break;
            case 'courses':
                $this->helper->generateCoursesMigration();
                break;
            case 'all':
                $this->generateAllMigrations();
                break;
            default:
                $this->showUsage();
        }
    }

    private function generateAllMigrations() {
        echo "Generating all migrations...\n";
        $this->helper->generateCategoriesMigration();
        $this->helper->generateCoursesMigration();
        echo "Migrations generated successfully.\n";
    }

    private function showUsage() {
        echo "Usage: php migrate.php [command]\n\n";
        echo "Commands:\n";
        echo "  all        Generate migrations for both categories and courses\n";
        echo "  categories Generate migration for categories only\n";
        echo "  courses    Generate migration for courses only\n";
    }
}

// Run the CLI
$cli = new MigrationCLI();
$cli->run($argv);