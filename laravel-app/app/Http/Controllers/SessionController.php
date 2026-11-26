<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SessionController extends Controller
{
    public function create(Request $request)
    {
        return $request->session()->has('user_id') ? redirect('/index.php') : view('login');
    }

    public function store(Request $request)
    {
        if ($request->input('action') === 'guest') {
            return $this->signIn($request, ['user_id' => 'guest', 'username' => 'guest', 'full_name' => 'Campus Guest', 'role' => 'Guest']);
        }
        $data = $request->validate(['username' => 'required|string|max:255', 'password' => 'required|string|min:6|max:255']);
        $identity = trim($data['username']);
        foreach (['sellers', 'users'] as $table) {
            if (!Schema::hasTable($table)) continue;
            $query = DB::table($table)->whereRaw('LOWER(email) = ?', [strtolower($identity)]);
            if ($table === 'users' && Schema::hasColumn($table, 'username')) {
                $query->orWhereRaw('LOWER(username) = ?', [strtolower($identity)]);
            }
            $user = $query->first();
            if ($user && Hash::check($data['password'], $user->password_hash ?? $user->password ?? '')) {
                return $this->signIn($request, ['user_id' => ($table === 'sellers' ? 'seller_' : '').$user->id, 'username' => $user->email, 'full_name' => $user->full_name ?? $user->owner_name ?? $user->name ?? $identity, 'role' => $table === 'sellers' ? 'seller' : ($user->role ?? 'Student')]);
            }
        }
        return back()->withErrors(['username' => 'The supplied credentials are incorrect.'])->withInput($request->only('username'));
    }

    private function signIn(Request $request, array $user)
    {
        $request->session()->regenerate();
        $request->session()->put($user);
        return redirect('/welcome.php');
    }

    public function destroy(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
