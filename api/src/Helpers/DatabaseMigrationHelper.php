<?php

namespace CourseCatalog\Helpers;

class DatabaseMigrationHelper {
    /**
     * Generate categories migration
     */
    public function generateCategoriesMigration() {
        $sourceFile = dirname(__DIR__, 3) . '/data/categories.json';

        if (!file_exists($sourceFile)) {
            throw new \Exception("Categories source file not found: $sourceFile");
        }

        $categoriesJson = file_get_contents($sourceFile);
        $categories = json_decode($categoriesJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error parsing categories JSON: " . json_last_error_msg());
        }

        $migrationContent = $this->generateUpDownMigration(
            'categories',
            $this->generateCategoriesTableSchema(),
            $this->generateCategoriesInsertValues($categories)
        );

        $filename = $this->generateMigrationFilename('categories');
        $this->writeMigrationFile($filename, $migrationContent);

        return $filename;
    }

    /**
     * Generate courses migration
     */
    public function generateCoursesMigration() {
        $sourceCourseFile = dirname(__DIR__, 3) . '/data/course_list.json';
        $sourceCategoryFile = dirname(__DIR__, 3) . '/data/categories.json';

        if (!file_exists($sourceCourseFile)) {
            throw new \Exception("Courses source file not found: $sourceCourseFile");
        }
        if (!file_exists($sourceCategoryFile)) {
            throw new \Exception("Categories source file not found: $sourceCategoryFile");
        }

        $coursesJson = file_get_contents($sourceCourseFile);
        $categoryJson = file_get_contents($sourceCategoryFile);

        $courses = json_decode($coursesJson, true);
        $categories = json_decode($categoryJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error parsing courses JSON: " . json_last_error_msg());
        }

        $categoriesMap = $this->createCategoriesMap($categories);

        $migrationContent = $this->generateUpDownMigration(
            'courses',
            $this->generateCoursesTableSchema(),
            $this->generateCoursesInsertValues($courses, $categoriesMap)
        );

        $filename = $this->generateMigrationFilename('courses');
        $this->writeMigrationFile($filename, $migrationContent);

        return $filename;
    }

    /**
     * Generate a migration with up and down sections
     */
    private function generateUpDownMigration($tableName, $createTableSchema, $insertValues) {
        return "-- up\n" .
            $createTableSchema . "\n\n" .
            $insertValues . "\n\n" .
            "-- down\n" .
            "DROP TABLE IF EXISTS `{$tableName}`;\n";
    }

    /**
     * Generate categories table schema
     */
    private function generateCategoriesTableSchema() {
        return "CREATE TABLE `categories` (
    `id` VARCHAR(36) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `parent_id` VARCHAR(36) DEFAULT NULL,
    `count_of_courses` INT DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";
    }

    /**
     * Generate courses table schema
     */
    private function generateCoursesTableSchema() {
        return "CREATE TABLE `courses` (
    `id` VARCHAR(36) PRIMARY KEY,
    `course_id` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `image_preview` VARCHAR(255),
    `category_id` VARCHAR(36) NOT NULL,
    `main_category_name` VARCHAR(255),
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";
    }

    /**
     * Generate categories insert values
     */
    private function generateCategoriesInsertValues($categories) {
        if (empty($categories)) {
            return "-- No categories to insert";
        }

        $insertValues = [];
        foreach ($categories as $category) {
            $parentId = $category['parent'] ? "'" . $this->escapeSql($category['parent']) . "'" : 'NULL';
            $insertValues[] = sprintf(
                "('%s', '%s', NULL, %s, 0)",
                $this->escapeSql($category['id']),
                $this->escapeSql($category['name']),
                $parentId
            );
        }

        return "INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `count_of_courses`) VALUES\n" .
            implode(",\n", $insertValues) . ";";
    }

    /**
     * Generate courses insert values
     */
    private function generateCoursesInsertValues($courses, $categoriesMap) {
        if (empty($courses)) {
            return "-- No courses to insert";
        }

        $insertValues = [];
        foreach ($courses as $course) {
            $mainCategoryName = $this->findMainCategoryName($course['category_id'], $categoriesMap);

            $insertValues[] = sprintf(
                "('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                $this->generateUuid(),
                $this->escapeSql($course['course_id']),
                $this->escapeSql($course['title']),
                $this->escapeSql($course['description']),
                $this->escapeSql($course['image_preview']),
                $this->escapeSql($course['category_id']),
                $this->escapeSql($mainCategoryName)
            );
        }

        return "INSERT INTO `courses` (`id`, `course_id`, `title`, `description`, `image_preview`, `category_id`, `main_category_name`) VALUES\n" .
            implode(",\n", $insertValues) . ";";
    }

    /**
     * Generate migration filename
     */
    private function generateMigrationFilename($type) {
        $migrationDir = dirname(__DIR__, 3) . '/database/migrations/';

        // Ensure migration directory exists
        if (!is_dir($migrationDir)) {
            mkdir($migrationDir, 0755, true);
        }

        return $migrationDir . date('YmdHis') . "_{$type}_migration.sql";
    }

    /**
     * Write migration file
     */
    private function writeMigrationFile($filename, $content) {
        file_put_contents($filename, $content);
        echo "Migration generated: " . basename($filename) . "\n";
    }

    /**
     * Create categories map for quick lookup
     */
    private function createCategoriesMap($categories) {
        $map = [];
        foreach ($categories as $category) {
            $map[$category['id']] = $category;
        }
        return $map;
    }

    /**
     * Find main category name by traversing category tree
     */
    private function findMainCategoryName($categoryId, $categoriesMap) {
        $currentCategory = $categoriesMap[$categoryId] ?? null;

        // Traverse up the category tree to find the root category
        while ($currentCategory && $currentCategory['parent']) {
            $currentCategory = $categoriesMap[$currentCategory['parent']] ?? null;
        }

        return $currentCategory ? $currentCategory['name'] : 'Uncategorized';
    }

    /**
     * Generate a UUID
     */
    private function generateUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Escape SQL special characters
     */
    private function escapeSql($str) {
        return str_replace("'", "''", $str);
    }

    /**
     * Generate both categories and courses migrations
     */
    public function generateAllMigrations() {
        $this->generateCategoriesMigration();
        $this->generateCoursesMigration();
    }
}