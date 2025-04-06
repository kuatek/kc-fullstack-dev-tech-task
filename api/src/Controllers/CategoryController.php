<?php

namespace CourseCatalog\Controllers;

use CourseCatalog\Repositories\CategoryRepository;
use Exception;

class CategoryController
{
    private CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all categories
     */
    public function getAllCategories()
    {
        try {
            $categories = $this->repository->getAllCategories();

            if (empty($categories)) {
                http_response_code(404);
                echo json_encode(['message' => 'No categories found']);
                return;
            }

            http_response_code(200);
            echo json_encode($categories);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to retrieve categories',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get a specific category by ID
     *
     * @param string $id
     */
    public function getCategoryById(string $id)
    {
        try {
            $category = $this->repository->getCategoryById($id);

            if (!$category) {
                http_response_code(404);
                echo json_encode(['message' => 'Category not found']);
                return;
            }

            http_response_code(200);
            echo json_encode($category);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to retrieve category',
                'message' => $e->getMessage()
            ]);
        }
    }
}