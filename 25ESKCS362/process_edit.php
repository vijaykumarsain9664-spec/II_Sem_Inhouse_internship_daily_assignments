# Student Management Portal

A complete, production-ready **Student Management Portal** built with PHP (PDO) + MySQL + Bootstrap 5.

## Features

**Core CRUD**
- Create — Add new student with full client + server-side validation (name, email, branch, CGPA)
- Read — All students in a responsive Bootstrap table
- Update — Pre-filled edit form, success alert on save
- Delete — JS confirm dialog + success message
- Search — Filter by name, email, or branch simultaneously

**Bonus features implemented**
- Responsive layout, Bootstrap Icons throughout, live record count
- **Profile photo upload** — stored as filename in MySQL, rendered via `<img>` (with initial-avatar fallback when no photo)
- **Student status** — Active / Inactive dropdown; table defaults to Active only, with a "View All" option
- **Course/branch filter** — dropdown driving a `WHERE branch = ?` query
- **Multi-field search** — name, email, branch, plus min/max CGPA range, all combined in one `WHERE` clause
- **Dashboard stats row** — Total Students, Average CGPA, Active Students, Top Branch, and a per-branch breakdown, all from SQL aggregate queries (`COUNT`, `AVG`, `GROUP BY`)

## Project structure

```
student-portal/
├── config.php              # DB connection + app constants
├── schema.sql               # Database schema + seed data
├── index.php                 # Dashboard: stats, filters, search, table
├── add_student.php           # Add form
├── process_add.php           # Add form handler (validation + upload + insert)
├── edit_student.php          # Pre-filled edit form
├── process_edit.php          # Edit handler (validation + upload + update)
├── delete_student.php        # Delete handler (+ removes uploaded photo file)
├── includes/
│   ├── header.php            # Shared navbar/head
│   └── footer.php             # Shared footer/scripts
├── assets/
│   └── custom.css             # Custom styling on top of Bootstrap
└── uploads/                    # Uploaded profile photos land here
```

## Setup

1. **Create the database**
   ```bash
   mysql -u root -p < schema.sql
   ```
   This creates the `student_portal` database, the `students` table (with indexes on
   `branch`, `status`, `cgpa`), and 8 sample rows.

2. **Configure credentials**

   Edit `config.php` and set your MySQL username/password:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'student_portal');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Make sure `uploads/` is writable**
   ```bash
   chmod 755 uploads
   ```

4. **Serve the app**

   With PHP's built-in server (quickest for local testing):
   ```bash
   php -S localhost:8000
   ```
   Then open `http://localhost:8000/index.php`.

   Or drop the folder into your XAMPP/WAMP/MAMP `htdocs`/`www` directory and browse to it via `http://localhost/student-portal/`.

## Security notes

- All queries use **PDO prepared statements** — no raw string interpolation of user input into SQL.
- All output is escaped via `htmlspecialchars()` (the `h()` helper) to prevent XSS.
- Uploaded files are validated by real MIME type (`mime_content_type`), size-limited to 2 MB, renamed
  with a unique generated filename (so a filename can never be used to overwrite another file or execute
  as PHP), and only image types are accepted.
- Email uniqueness is enforced both at the database level (`UNIQUE` constraint) and in application logic
  (with a friendly error message instead of a raw DB error).

## Notes / possible next steps

- Add pagination if the student list grows large.
- Add authentication (login) if this needs to be restricted to staff.
- Move validation error/old-input passing from GET query-string (base64 JSON) to PHP sessions for cleaner
  URLs — kept as GET here for simplicity/statelessness, but sessions would be a natural next step.
