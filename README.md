Task Management System

A Laravel-based task management system with authentication, real-time interactivity, project/task CRUD, AJAX task updates, filters, search, and a user activity log for developer testing.

## Features
- Project and Task CRUD
- AJAX-based updates
- User Activity Log
- Search & Filter Tasks
- Task Completion Tracker

**** Setup Instructions****

1. Clone Repository
   ->git clone https://github.com/niralivachhani26/laravel-task-manager.git
   ->cd laravel-task-manager

2. Install Dependency using cmd
   ->composer install
   ->npm install
   ->npm run dev

3. Environment Setup
   ->cp .env.example .env
   ->php artisan key:generate

4. Database Configuration
   ->Update .env with your database credentials:
	DB_DATABASE=task_manager
	DB_USERNAME=root
	DB_PASSWORD=

5. Run Migrations and Seeders
   ->php artisan migrate --seed

6. Run the Application
   ->php artisan serve


**** Demo Credentials****

Use the following credentials to log into the demo account:

Email: vnirali261997@gmail.com
password: admin123456


**** TechStack Used****

 ->Backend: Laravel (PHP)

 ->Frontend: Blade, jQuery, Bootstrap, AJAX

 ->Authentication: Laravel Breeze

 ->Database: MySQL

 ->Others:

	Laravel Validation (backend)

	FontAwesome (icons)


**** Assumptions & Notes****

  1. Only authenticated users can access the task management interface.
  2. User activities like task creation, updates, and deletions are logged.
  3. To update a task status, simply click on the task status badge/text; it will toggle between statuses instantly using AJAX.
