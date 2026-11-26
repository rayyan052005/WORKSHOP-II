<?php
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/login.php', [SessionController::class, 'create'])->name('login');
Route::post('/login.php', [SessionController::class, 'store'])->middleware('throttle:10,1');
Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
Route::get('/{page?}', function (string $page = 'index.php') {
    $pages = ['index.php' => ['index', 'Explore Food', 'explore'], 'favorites.php' => ['favorites', 'Favorites', 'favorites'], 'orders.php' => ['orders', 'My Orders', 'orders'], 'vendor.php' => ['vendor', 'Vendor & Seller Portal', 'vendor'], 'welcome.php' => ['welcome', 'Welcome', 'welcome']];
    abort_unless(isset($pages[$page]), 404);
    if (!session()->has('user_id')) return redirect()->route('login');
    if ($page === 'vendor.php' && strtolower(session('role', '')) !== 'seller') abort(403);
    [$view, $title, $activePage] = $pages[$page];
    return view($view, ['pageTitle' => 'UniFood | '.$title, 'activePage' => $activePage]);
});
