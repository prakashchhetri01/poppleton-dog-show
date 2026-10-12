# Poppleton Dog Show

A PHP/MySQL database-driven website by **Prakash Bahadur G C**, for **CIS2360: Relational Databases and Web Integration**, University of Huddersfield. Original coursework submitted on **19 December 2025**.

## Features and skills

Login with password hashing and PHP sessions; owner, dog and event totals; up to ten dogs with multiple scored entries, ranked by average score by default; dog/owner search; owner detail and email links; admin statistics and admin-only user registration.

Demonstrates PHP, MySQL joins and aggregation, prepared statements, session management, escaped HTML output, form labels and skip links on several pages.

## Files

| Path | Purpose |
| --- | --- |
| `public/` | Six PHP files recovered from the report appendix. |
| `sql/01_demo_schema.sql` | New schema reconstructed from PHP references. |
| `sql/02_demo_data.sql` | New fictional demonstration records. |
| `scripts/create_admin.php` | New CLI-only initial administrator setup. |

## Local setup

Use XAMPP or another local PHP/MySQL environment. PHP needs MySQLi with mysqlnd for `get_result()`. The admin script also uses readline.

1. Start MySQL/MariaDB. In phpMyAdmin import `sql/01_demo_schema.sql`, then `sql/02_demo_data.sql` into a fresh demo database.
2. Configure connection settings. Local defaults: host `localhost`, user `root`, empty password, database `poppleton`. Override with `DB_HOST`, `DB_USER`, `DB_PASSWORD` and `DB_NAME` environment variables if needed. Never commit real credentials.
3. From the repository root create the first administrator:

```bash
php scripts/create_admin.php
```

4. Serve only the `public` directory:

```bash
php -S 127.0.0.1:8000 -t public
```

5. Open `http://127.0.0.1:8000/login.php` and sign in with your new account. Alternatively configure Apache to serve this project's `public` directory. XAMPP users may need the full path to their PHP executable.

Keep SQL and setup scripts outside the served directory. The setup password is visible in the local terminal.

## Expected demo results

2 owners, 3 dogs and 2 events. Demo Bella averages 92, followed by Demo Buddy at 85. Demo Max has only one entry and is excluded. Admin statistics: 2 competitions, 2 represented breeds, overall average 89.80.

## Recovery and changes

The original SQL import and dog images were not supplied. The demo schema and sample records are new reconstructions, not the original assessment database. The extracted index page has no image rendering, so this edition does not include the report's image gallery.

Changes include environment-based connection configuration, UTF-8 database connections, generic database errors, session ID regeneration after login, full grouping of nonaggregated selected fields, missing-role handling during registration and null-safe owner output. The CLI admin script solves the initial account setup requirement.

## Verification and limitations

PHP/MySQL are unavailable in the preparation environment. PHP syntax, authentication/session flows and actual MySQL execution have **not been runtime-tested**. Verify locally in XAMPP before presenting this as a working deployed application.

This is a local educational prototype. It lacks CSRF protection, login rate limiting and production session/cookie hardening. Logout uses GET; registration password validation is less strict than the initial-admin script. It is not ready for public deployment.

The ranking counts scored entries, not distinct events. Search filters before ranking. Alphabetical sorting changes which ten qualifying dogs are selected. Sorting is available via `?sort=dog`, `?sort=breed`, `?sort=score` or `?sort=owner`, but there are no visible sorting buttons in the appendix code.

Future improvements: restore original SQL/assets, test the complete workflow, add CSRF protection and rate limiting, improve validation, count distinct events if required and add visible sorting controls that preserve the top-ten-by-score set.
