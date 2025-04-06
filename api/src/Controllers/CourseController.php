<?php

namespace CourseCatalog\Controllers;

use CourseCatalog\Repositories\CourseRepository;
use Exception;

class CourseController
{
    private CourseRepository $repository;

    public function __construct(CourseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get courses, optionally filtered by category
     */
    public function getCourses()
    {
        try {
            // Check for category filter
            $categoryId = $_GET['category_id'] ?? null;

            $courses = $this->repository->getCourses($categoryId);

            if (empty($courses)) {
                http_response_code(404);
                echo json_encode(['message' => 'No courses found']);
                return;
            }

            http_response_code(200);
            echo json_encode($courses);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to retrieve courses',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get a specific course by ID
     *
     * @param string $id
     */
    public function getCourseById(string $id)
    {
        try {
            $course = $this->repository->getCourseById($id);

            if (!$course) {
                http_response_code(404);
                echo json_encode(['message' => 'Course not found']);
                return;
            }

            http_response_code(200);
            echo json_encode($course);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to retrieve course',
                'message' => $e->getMessage()
            ]);
        }
    }
}