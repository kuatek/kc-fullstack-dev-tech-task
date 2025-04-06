<?php
require_once __DIR__ . '/vendor/autoload.php';


ini_set('display_errors', 1);
error_reporting(E_ALL);

use CourseCatalog\Controllers\CategoryController;
use CourseCatalog\Controllers\CourseController;
use CourseCatalog\Repositories\CategoryRepository;
use CourseCatalog\Repositories\CourseRepository;

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection
$host = 'database.cc.localhost';
$dbname = 'course_catalog';
$username = 'test_user';
$password = 'test_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Routing
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string from URI
$path = parse_url($requestUri, PHP_URL_PATH);
$path = rtrim($path, '/');

// Initialize repositories and controllers
$categoryRepository = new CategoryRepository($pdo);
$courseRepository = new CourseRepository($pdo);
$categoryController = new CategoryController($categoryRepository);
$courseController = new CourseController($courseRepository);

// Simple routing
try {
    switch ($path) {
        case '/categories':
            if ($requestMethod === 'GET') {
                $categoryController->getAllCategories();
            }
            break;

        case (preg_match('/^\/categories\/([^\/]+)$/', $path, $matches) ? true : false):
            if ($requestMethod === 'GET') {
                $categoryController->getCategoryById($matches[1]);
            }
            break;

        case '/courses':
            if ($requestMethod === 'GET') {
                $courseController->getCourses();
            }
            break;

        case (preg_match('/^\/courses\/([^\/]+)$/', $path, $matches) ? true : false):
            if ($requestMethod === 'GET') {
                $courseController->getCourseById($matches[1]);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}