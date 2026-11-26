# UniFood Laravel

The Laravel application is in `laravel-app/`. The original PHP application remains at the project root for reference. Open the Laravel application using the instructions below to use the migrated system.

## Run

```powershell
cd "C:\laragon\WORKSHOP II\laravel-app"
php artisan serve
```

Open http://127.0.0.1:8000/login.php and choose guest access, or create an account:

```powershell
php artisan unifood:create-user
```

The command prompts for name, email, Student/seller role, and a hidden password. Seller accounts can access the vendor portal. Login checks stored password hashes; the original arbitrary-password demo fallback has been removed.

For Laragon/Apache hosting, point the virtual host document root to `C:\laragon\WORKSHOP II\laravel-app\public`. Do not expose the Laravel application directory as the document root.

## Implementation

- Laravel 12 with installed Composer dependencies and a lockfile.
- Existing explore, favorites, orders, welcome, login, and vendor screens converted to Blade views.
- Laravel routes preserve `.php` page URLs used by existing JavaScript.
- Laravel sessions, CSRF tokens, login rate limiting, session regeneration, and vendor role checks.
- A new local SQLite database; file sessions and cache avoid requiring a database server for guest access.
- Database login supports the Laravel users table and the original users/sellers password_hash columns when configured against an existing database.

Orders, favorites, the cart, and vendor menu changes retain the existing JavaScript/localStorage behavior. They are browser-local demo data, not shared database records. Existing native PHP login sessions are not transferred; sign in again.

To connect an existing database, configure `.env` DB settings and enable the matching PHP PDO driver. The current PHP installation does not enable pdo_pgsql. Back up an existing database and review migrations before applying them there; the skeleton migrations assume a new database.

## Verify

```powershell
php artisan test
php artisan view:cache
```

The feature suite verifies guest redirects, login rendering, database password checking, guest pages, logout, vendor restrictions, and vendor page rendering.
