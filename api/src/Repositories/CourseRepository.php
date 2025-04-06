<?php

namespace CourseCatalog\Repositories;

use PDO;
use Exception;

class CourseRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get all courses, optionally filtered by category
     *
     * @param string|null $categoryId
     * @return array
     */
    public function getCourses(?string $categoryId = null): array
    {
        $query = "
            WITH RECURSIVE category_tree AS (
                SELECT 
                    id, 
                    parent_id
                FROM categories
                WHERE id = :category_id

                UNION ALL

                SELECT 
                    c.id, 
                    c.parent_id
                FROM categories c
                JOIN category_tree ct ON c.parent_id = ct.id
            )
            SELECT 
                c.id, 
                c.course_id, 
                c.title, 
                c.description, 
                c.image_preview, 
                c.main_category_name
            FROM courses c
            WHERE 1=1 " .
            ($categoryId ? "AND c.category_id IN (SELECT id FROM category_tree)" : "") .
            " ORDER BY c.title";

        $stmt = $this->connection->prepare($query);
        $params = $categoryId ? ['category_id' => $categoryId] : [];
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific course by ID
     *
     * @param string $id
     * @return array|null
     */
    public function getCourseById(string $id): ?array
    {
        $query = "
            SELECT 
                id, 
                course_id, 
                title, 
                description, 
                image_preview, 
                main_category_name
            FROM courses 
            WHERE id = :id";

        $stmt = $this->connection->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}