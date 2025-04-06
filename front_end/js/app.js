class CourseCatalog {
    constructor() {
        this.categoryList = document.getElementById('category-list');
        this.coursesGrid = document.getElementById('courses-grid');
        this.apiBaseUrl = 'http://api.cc.localhost';

        this.init();
    }

    async init() {
        await this.loadCategories();
        await this.loadCourses();
        this.setupEventListeners();
    }

    async loadCategories() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/categories`);
            const categories = await response.json();

            // Clear existing categories
            this.categoryList.innerHTML = `
                <li class="active" data-category-id="all">
                    Course catalog
                    <span class="course-count">${this.calculateTotalCourses(categories)}</span>
                </li>
            `;

            // Organize categories by hierarchy
            const categoriesMap = this.organizeCategories(categories);

            // Render categories recursively
            this.renderCategories(categoriesMap, null, 0);
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    organizeCategories(categories) {
        const categoriesMap = new Map();
        const rootCategories = [];

        // Create a map of categories
        categories.forEach(category => {
            category.children = [];
            categoriesMap.set(category.id, category);
        });

        // Build hierarchy
        categories.forEach(category => {
            if (category.parent_id) {
                const parentCategory = categoriesMap.get(category.parent_id);
                if (parentCategory) {
                    parentCategory.children.push(category);
                }
            } else {
                rootCategories.push(category);
            }
        });

        return rootCategories;
    }

    renderCategories(categories, parentLi = null, depth = 0) {
        categories.forEach(category => {
            const li = document.createElement('li');
            li.dataset.categoryId = category.id;
            li.innerHTML = `
                ${category.name}
                <span class="course-count">${category.count_of_courses}</span>
            `;

            // Indent based on depth
            li.style.paddingLeft = `${depth * 15}px`;

            if (parentLi) {
                parentLi.appendChild(li);
            } else {
                this.categoryList.appendChild(li);
            }

            // Render children recursively
            if (category.children && category.children.length > 0) {
                this.renderCategories(category.children, li, depth + 1);
            }
        });
    }

    calculateTotalCourses(categories) {
        return categories.reduce((total, category) => total + category.count_of_courses, 0);
    }

    async loadCourses(categoryId = null) {
        try {
            const url = categoryId && categoryId !== 'all'
                ? `${this.apiBaseUrl}/courses?category_id=${categoryId}`
                : `${this.apiBaseUrl}/courses`;

            const response = await fetch(url);
            const courses = await response.json();

            // Clear existing courses
            this.coursesGrid.innerHTML = '';

            // Render courses
            courses.forEach(course => {
                const courseCard = this.createCourseCard(course);
                this.coursesGrid.appendChild(courseCard);
            });

            // Update page title
            this.updatePageTitle(categoryId);
        } catch (error) {
            console.error('Error loading courses:', error);
        }
    }

    updatePageTitle(categoryId) {
        const pageTitle = document.querySelector('header h1');
        if (categoryId && categoryId !== 'all') {
            const selectedCategory = document.querySelector(`[data-category-id="${categoryId}"]`);
            pageTitle.textContent = selectedCategory ? selectedCategory.textContent.split('\n')[0].trim() : 'Course catalog';
        } else {
            pageTitle.textContent = 'Course catalog';
        }
    }

    createCourseCard(course) {
        const card = document.createElement('div');
        card.classList.add('course-card');

        card.innerHTML = `
            <div class="course-image">
                <img src="${course.image_preview}" alt="${course.title}">
            </div>
            <div class="course-details">
                <p class="course-category">${course.main_category_name}</p>
                <h3>${course.title}</h3>
                <p class="course-description">${course.description}</p>
            </div>
        `;

        return card;
    }

    setupEventListeners() {
        this.categoryList.addEventListener('click', (event) => {
            const li = event.target.closest('li');
            if (li) {
                // Remove active class from all categories
                this.categoryList.querySelectorAll('li').forEach(item =>
                    item.classList.remove('active')
                );

                // Add active class to clicked category
                li.classList.add('active');

                // Load courses for the selected category
                const categoryId = li.dataset.categoryId;
                this.loadCourses(categoryId);
            }
        });
    }
}

// Initialize the application when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    new CourseCatalog();
});