# UniFood

The Laravel version is in [laravel-app](laravel-app/README.md).

Run from this folder:

```powershell
cd laravel-app
php artisan serve
```

Then visit http://127.0.0.1:8000/login.php. Guest access is available. To create a student or seller account, run `php artisan unifood:create-user` inside laravel-app.

The PHP files at the root are the original version. Laravel hosting must use laravel-app/public as its document root.
