# TODO: Fix Bugs in Attendance Project

## Database Schema Issues
- [x] Update `sql/schema.sql` to use `users` table instead of `employees`, add missing columns: `approved` (TINYINT), `status` (ENUM), `twofa_secret` (VARCHAR), `face_descriptor` (MEDIUMTEXT), etc.
- [x] Ensure consistency: `approved` as TINYINT(1), `status` as ENUM('pending','approved','rejected','active')

## Database Connection Bug
- [x] Fix `includes/db.php`: Use the constructed `$dsn` in PDO constructor instead of hardcoded string.

## Authentication and Session Issues
- [x] Standardize `session_start()` placement: Move to top of files before any output or requires.
- [x] Fix `public/auth/verify_face.php`: Set `$_SESSION['role']` based on user role after login.
- [x] Implement 2FA in `admin/login.php`: If user has `twofa_secret`, set `$_SESSION['tmp_admin_id']` and redirect to `/auth/admin_2fa.php`.
- [x] Fix `includes/auth.php`: Ensure `checkAuth` redirects correctly (seems ok, but verify).

## Missing Functions
- [x] Add missing functions in `includes/functions.php`:
  - `getEmployeeCount($pdo)`
  - `getPendingApprovals($pdo)`
  - `getTodayAttendanceCount($pdo)`
  - `fetch_all($query)`
  - `esc($str)` for escaping SQL strings (though better to use prepared statements)
- [x] Fix `admin/index.php`: Use `db()` instead of `$pdo`.


## Table Name Inconsistencies
- [x] Update all queries from `employees` to `users` in `admin/reports.php` and other files.
- [x] Ensure joins use `users` table.

## Other Fixes
- [x] In `admin/reports.php`: Replace `esc()` and `fetch_all()` with proper prepared statements or define the functions.
- [x] Test all login flows, approvals, reports after fixes.

## Design Improvements
- [x] Updated admin/login.php with modern Bootstrap design, gradients, icons, and responsive layout.
- [x] Updated employee/login.php with attractive design, added face login option, and registration link.
- [x] Redesigned admin/index.php with navbar, welcome section, animated cards, and quick actions.
- [x] Redesigned public/auth/register.php with modern UI, password strength indicator, validation, and responsive design.
- [x] Redesigned admin/users.php with modern table design, user avatars, status badges, and improved UX.
- [x] Redesigned public/auth/login.php with modern UI, face recognition instructions, success/error messages, and responsive design.

## Admin Account Setup
- [x] Created initial admin user with credentials:
  - Email: admin@example.com
  - Password: admin123
  - Please change password after first login

## Testing
- [ ] Run the application and check for errors.
- [ ] Verify database queries work with updated schema.
