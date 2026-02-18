# Job Portal Web Application

A production-oriented, dynamic Job Portal built with PHP, MySQL, Bootstrap 5, and vanilla JavaScript.

## Core Features
- Role-based authentication for admin, employer, and job seeker
- Job posting and approval workflow
- AJAX-powered job search filters
- Job applications with dashboard tracking
- Contact message management
- CSRF protection, prepared statements, and secure password hashing

## Quick Start
1. Create a MySQL database and import `database.sql`.
2. Update DB credentials in `config/database.php`.
3. Start server:
   ```bash
   php -S 0.0.0.0:8080 -t .
   ```
4. Open `http://localhost:8080/index.php`.

## Default Roles
- Register as employer or job seeker from `/register.php`
- Insert an admin account directly into `users` table with `role='admin'`
