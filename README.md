# Homa Bay County Citizen Portal (MVP)

A working prototype inspired by digital-services work during my industrial
attachment with the County Government of Homa Bay, Department of Blue
Economy, Mining, Fisheries and Digital Economy. Built to demonstrate a
citizen-facing county information portal with an admin backend.

## Features

- **Public site**: department directory, announcements/notices feed, single
  announcement and department pages, a citizen feedback/complaint form.
- **Admin dashboard**: secure login, post/delete announcements, review and
  update the status of citizen feedback (new → in review → resolved).
- Relational schema with proper foreign keys (departments → announcements,
  departments → feedback), demonstrating one-to-many relationships and joins.

## Tech stack

PHP (procedural, PDO for database access), MySQL, HTML/CSS — no framework
dependency, so it's easy to read, deploy, and explain in an interview.

## Setup

1. Create the database:
   ```
   mysql -u root -p < sql/schema.sql
   ```
2. Update `config/db.php` with your database credentials.
3. Serve the folder with PHP's built-in server for local testing:
   ```
   php -S localhost:8000
   ```
4. Visit `http://localhost:8000/admin/setup.php` **once** to create your
   admin account, then delete or rename `admin/setup.php` (it will refuse
   to run again once an admin exists, but removing it is safer).
5. Log in at `/admin/login.php` to post announcements and manage feedback.

## Project structure

```
citizen-portal/
├── admin/          # login, dashboard, logout, one-time setup
├── config/         # database connection
├── css/            # stylesheet
├── includes/       # shared header/footer
├── sql/            # schema + seed data
├── index.php
├── departments.php
├── department.php
├── announcement.php
└── feedback.php
```

## Notes on security choices

- Passwords are hashed with `password_hash()` / verified with
  `password_verify()` — never stored in plain text.
- All database queries use prepared statements (PDO) to prevent SQL
  injection.
- All output is escaped with `htmlspecialchars()` to prevent XSS.
- Admin pages are protected by a session check (`admin/auth.php`).

## Possible next steps

- Role-based access so department staff only manage their own department's
  announcements and feedback.
- Email notification to citizens when their feedback status changes.
- Pagination for announcements once volume grows.
- File/document uploads attached to announcements.
