# Task Management Application

## Requirements
- PHP 8.0+
- Composer
- MySQL

## Installation
1. Clone repository
2. `composer install`
3. Copy `.env.example` to `.env`
4. Configure database in `.env`
5. `php artisan key:generate`
6. `php artisan migrate`
7. `php artisan serve`

## Usage
- Access application at `http://localhost:8000`
- Create projects first
- Add tasks to projects
- Drag & drop to reorder tasks
