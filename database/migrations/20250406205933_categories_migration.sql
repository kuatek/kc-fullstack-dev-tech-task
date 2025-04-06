-- up
CREATE TABLE `categories` (
    `id` VARCHAR(36) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `parent_id` VARCHAR(36) DEFAULT NULL,
    `count_of_courses` INT DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `count_of_courses`) VALUES
('1c2a3b4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d', 'Technology', NULL, NULL, 0),
('2c3d4e5f-6a7b-8c9d-0e1f-2a3b4c5d6e7f', 'Software Development', NULL, '1c2a3b4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d', 0),
('3d4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f8a', 'Hardware Engineering 2', NULL, '2c3d4e5f-6a7b-8c9d-0e1f-2a3b4c5d6e7f', 0),
('3d4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f82', 'Hardware Engineering 3', NULL, '3d4e5f6a-7b8c-9d0e-1f2a-3b4c5d6e7f8a', 0),
('4e5f6a7b-8c9d-0e1f-2a3b-4c5d6e7f8a9b', 'Education', NULL, NULL, 0),
('5f6a7b8c-9d0e-1f2a-3b4c-5d6e7f8a9b0c', 'Higher Education', NULL, '4e5f6a7b-8c9d-0e1f-2a3b-4c5d6e7f8a9b', 0),
('6a7b8c9d-0e1f-2a3b-4c5d-6e7f8a9b0c1d', 'K-12 Education', NULL, '4e5f6a7b-8c9d-0e1f-2a3b-4c5d6e7f8a9b', 0),
('7b8c9d0e-1f2a-3b4c-5d6e-7f8a9b0c1d2e', 'Health & Wellness', NULL, NULL, 0),
('8c9d0e1f-2a3b-4c5d-6e7f-8a9b0c1d2e3f', 'Fitness & Nutrition', NULL, '7b8c9d0e-1f2a-3b4c-5d6e-7f8a9b0c1d2e', 0),
('9d0e1f2a-3b4c-5d6e-7f8a-9b0c1d2e3f4a', 'Mental Health', NULL, '7b8c9d0e-1f2a-3b4c-5d6e-7f8a9b0c1d2e', 0),
('0e1f2a3b-4c5d-6e7f-8a9b-0c1d2e3f4a5b', 'Arts & Entertainment', NULL, NULL, 0),
('1f2a3b4c-5d6e-7f8a-9b0c-1d2e3f4a5b6c', 'Visual Arts', NULL, '0e1f2a3b-4c5d-6e7f-8a9b-0c1d2e3f4a5b', 0),
('2a3b4c5d-6e7f-8a9b-0c1d-2e3f4a5b6c7d', 'Performing Arts', NULL, '0e1f2a3b-4c5d-6e7f-8a9b-0c1d2e3f4a5b', 0),
('3b4c5d6e-7f8a-9b0c-1d2e-3f4a5b6c7d8e', 'Science & Nature', NULL, NULL, 0),
('4c5d6e7f-8a9b-0c1d-2e3f-4a5b6c7d8e9f', 'Biology', NULL, '3b4c5d6e-7f8a-9b0c-1d2e-3f4a5b6c7d8e', 0),
('5d6e7f8a-9b0c-1d2e-3f4a-5b6c7d8e9f0a', 'Physics', NULL, '3b4c5d6e-7f8a-9b0c-1d2e-3f4a5b6c7d8e', 0),
('6e7f8a9b-0c1d-2e3f-4a5b-6c7d8e9f0a1b', 'Food & Cooking', NULL, NULL, 0),
('7f8a9b0c-1d2e-3f4a-5b6c-7d8e9f0a1b2c', 'Recipes', NULL, '6e7f8a9b-0c1d-2e3f-4a5b-6c7d8e9f0a1b', 0),
('8a9b0c1d-2e3f-4a5b-6c7d-8e9f0a1b2c3d', 'Culinary Techniques', NULL, '6e7f8a9b-0c1d-2e3f-4a5b-6c7d8e9f0a1b', 0),
('9b0c1d2e-3f4a-5b6c-7d8e-9f0a1b2c3d4e', 'Travel & Tourism', NULL, NULL, 0),
('0c1d2e3f-4a5b-6c7d-8e9f-0a1b2c3d4e5f', 'Destinations', NULL, '9b0c1d2e-3f4a-5b6c-7d8e-9f0a1b2c3d4e', 0),
('1d2e3f4a-5b6c-7d8e-9f0a-1b2c3d4e5f6a', 'Travel Tips', NULL, '9b0c1d2e-3f4a-5b6c-7d8e-9f0a1b2c3d4e', 0);

-- down
DROP TABLE IF EXISTS `categories`;
