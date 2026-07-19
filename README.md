# Task Management System

A Laravel-based task management system featuring authentication, real-time interactivity, project/task CRUD, asynchronous task updates, multi-criteria filtering, search capabilities, and an integrated user activity log optimized for developer testing.

---

### 🚀 Core Features

*   **Project & Task CRUD:** Complete create, read, update, and delete actions with relational consistency between items.
*   **AJAX-Based Updates:** Dynamic, page-refresh-free status updates for seamless workflows.
*   **User Activity Log:** Automated tracking that registers user behavior for accountability.
*   **Advanced Search & Filters:** High-performance filtering tools to isolate tasks rapidly.
*   **Task Completion Tracker:** Visual layout to monitor ongoing project progression.

---

### 🛠️ Tech Stack & Architecture

| Layer | Technologies |
| :--- | :--- |
| **Backend Framework** | Laravel (PHP) |
| **Frontend Architecture** | Blade, jQuery, Bootstrap, AJAX |
| **Authentication Layer** | Laravel Breeze |
| **Database Engine** | MySQL |
| **Assets & Utilities** | FontAwesome Icons, Laravel Server-side Validation |

---

### 💻 Local Installation & Setup Instructions

Follow these steps to deploy and execute the application environment locally:

#### 1. Clone the Infrastructure
```bash
git clone [https://github.com/niralivachhani26/laravel-task-manager.git](https://github.com/niralivachhani26/laravel-task-manager.git)
cd laravel-task-manager

# Install PHP backend dependencies
composer install

# Install and compile frontend assets
npm install
npm run dev

#Environment sync
cp .env.example .env
php artisan key:generate

#Database setup
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=

#Run Database Migrations & Seeders
php artisan migrate --seed

php artisan serve
