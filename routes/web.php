<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// session
Route::get('create-session', function () {
    session()->put('name', 'Giangpzo');
    $name = session('name');
    setcookie('BE_cookie','this is data that saved in BE');
    return 'created name session';
});

Route::get('/get-session', function () {
    $name = session('name');
    return 'name: ' . $name;
});

Route::get('/clear-session', function () {
    session()->flush();
    return 'session cleared';
});

//# Fake Login View for non-json request to avoid Exception
Route::get('login', function () {
    return view('welcome');
})->name('login');