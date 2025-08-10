<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CompanyController;

// Главная страница


// Только для авторизованных пользователей
Route::middleware('auth')->group(function () {
    // Главная страница
    Route::get('/', fn() => view('welcome'))->name('home');
    // Компании
    Route::resource('companies', CompanyController::class);


    // Контакты
    Route::get('/contacts', fn() => view('stub', ['title' => 'Контакты']))->name('contacts.index');

    // Проекты
    Route::get('/projects', fn() => view('stub', ['title' => 'Проекты']))->name('projects.index');

    // Справочники
    Route::get('/lectors', fn() => view('stub', ['title' => 'Лектора']))->name('lectors.index');
    Route::get('/regions', fn() => view('stub', ['title' => 'Регионы']))->name('regions.index');
    Route::get('/sferas', fn() => view('stub', ['title' => 'Сферы деятельности']))->name('sferas.index');
    Route::get('/interes', fn() => view('stub', ['title' => 'Темы']))->name('interes.index');
    Route::get('/statuses', fn() => view('stub', ['title' => 'Статусы']))->name('statuses.index');
    Route::get('/our_companies', fn() => view('stub', ['title' => 'Наши компании']))->name('our_companies.index');

    // Задачи
    Route::get('/tasks', fn() => view('stub', ['title' => 'Задачи']))->name('tasks.index');

    // Профиль
    Route::get('/profile', fn() => view('stub', ['title' => 'Профиль']))->name('profile.edit');
});

// Подключение маршрутов Breeze (логин, регистрация и т.п.)
require __DIR__ . '/auth.php';
