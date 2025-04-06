<?php

namespace CourseCatalog\Repositories;

use PDO;
use Exception;

class CategoryRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get all categories with course count
     *
     * @return array
     */
    public function getAllCategories(): array
    {
        $query = "
            WITH RECURSIVE category_tree AS (
                SELECT 
                    id, 
                    name, 
                    parent_id, 
                    0 AS depth
                FROM categories
                WHERE parent_id IS NULL

                UNION ALL

                SELECT 
                    c.id, 
                    c.name, 
                    c.parent_id, 
                    ct.depth + 1
                FROM categories c
                JOIN category_tree ct ON c.parent_id = ct.id
                WHERE ct.depth < 3
            )
            SELECT 
                ct.id, 
                ct.name, 
                (
                    SELECT COUNT(DISTINCT co.id) 
                    FROM courses co
                    WHERE co.category_id IN (
                        SELECT id FROM category_tree 
                        WHERE id = ct.id OR parent_id = ct.id
                    )
                ) AS count_of_courses
            FROM category_tree ct
            ORDER BY ct.name";

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific category by ID
     *
     * @param string $id
     * @return array|null
     */
    public function getCategoryById(string $id): ?array
    {
        $query = "
            SELECT 
                id, 
                name, 
                description, 
                parent_id,
                (
                    SELECT COUNT(DISTINCT id) 
                    FROM courses 
                    WHERE category_id IN (
                        WITH RECURSIVE category_tree AS (
                            SELECT id FROM categories WHERE id = :id
                            UNION
                            SELECT c.id 
                            FROM categories c
                            JOIN category_tree ct ON c.parent_id = ct.id
                        )
                        SELECT id FROM category_tree
                    )
                ) AS count_of_courses
            FROM categories 
            WHERE id = :id";

        $stmt = $this->connection->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}