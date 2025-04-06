# Course Catalog

## Project Overview
This is a full-stack course catalog application with a PHP backend API and a vanilla JavaScript frontend.

## Prerequisites
- Docker
- Docker Compose
- apache rewrite mode (a2enmod rewrite)
- pdo_mysql extension (docker-compose exec api docker-php-ext-install pdo pdo_mysql)
- Don't forget to restart apache (sudo service apache2 restart)

## Project Structure
- `api/`: PHP Backend API
- `front_end/`: Frontend application
- `database/`: Database migrations
- `docker-compose.yml`: Docker configuration

## Setup and Installation

### 1. Clone the Repository
```bash
git clone https://github.com/kuatek/kc-fullstack-dev-tech-task
```

### 2. Start the Application
```bash
docker-compose up --build
```

### 3. Access the Application
- Frontend: http://cc.localhost
- API: http://api.cc.localhost
- Traefik Dashboard: http://127.0.0.1:8080/dashboard/

### 4. Database Credentials
- Host: db.cc.localhost
- Database: course_catalog
- Username: test_user
- Password: test_password

## API Endpoints
- `GET /categories`: Retrieve all categories
- `GET /categories/{id}`: Retrieve a specific category
- `GET /courses`: Retrieve all courses
- `GET /courses/{id}`: Retrieve a specific course
- `GET /courses?category_id={categoryId}`: Filter courses by category

## Development Notes
- Follows PSR-12 coding standards
- Minimal SQL queries
- Recursive queries for category hierarchy
- Responsive frontend design

## Troubleshooting
- Ensure Docker is running
- Check Docker logs for any initialization issues
- Verify network connectivity
