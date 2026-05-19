# Travel Guide - Task 3 Admin Panel
Student ID: 22-49646-3  
Technology: PHP MVC, MySQL, PDO

## How to run
1. Copy this folder into `htdocs` or any local PHP server folder.
2. Import `database/schema.sql` in phpMyAdmin.
3. Update DB info in `config/database.php` if needed.
4. Run from the `public` folder.
5. Login: `admin@test.com` / `admin12345`

## Features
- Admin session gate
- Dashboard summary
- Add user with password_hash
- Verify/unverify user using AJAX JSON
- Delete user with related data cleanup
- Approve/reject post request
- Edit/delete posts
- Delete comments using AJAX JSON
- PDO prepared statements
- CSRF token check
- XSS protection using htmlspecialchars
- JS validation and PHP validation

## Folder structure
- config: database and helper functions
- controllers: request handling
- models: database queries
- views: HTML pages
- public: web files and assets
- api: AJAX endpoint
- database: SQL file
