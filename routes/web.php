<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Prompt 2: base layout + home page.
| Auth routes (login / register / logout) are stubs until Breeze is
| installed in Prompt 10. Replace this whole block at that point.
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// ── Stub auth routes (replaced by Breeze in Prompt 10) ──────────────────
Route::get('/login',    fn () => redirect('/'))->name('login');
Route::get('/register', fn () => redirect('/'))->name('register');
Route::post('/logout',  fn () => redirect('/'))->name('logout');
