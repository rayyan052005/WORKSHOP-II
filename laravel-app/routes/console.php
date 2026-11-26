<?php
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

Artisan::command('unifood:create-user', function () {
    $name = $this->ask('Full name');
    $email = $this->ask('Email');
    $role = $this->choice('Account role', ['Student', 'seller'], 0);
    $password = $this->secret('Password (at least 8 characters)');
    $validator = Validator::make(compact('name', 'email', 'password'), ['name' => 'required|string|max:255', 'email' => 'required|email|unique:users,email', 'password' => 'required|string|min:8']);
    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) $this->error($error);
        return 1;
    }
    $user = new User;
    $user->name = $name;
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->role = $role;
    $user->save();
    $this->info('Account created. Sign in with this email and password.');
})->purpose('Create a UniFood student or seller account');
