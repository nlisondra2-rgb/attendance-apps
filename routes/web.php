<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/welcome', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/home', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/admin', function () {
    return Inertia::render('Admin');
})->name('admin');

Route::get('/student', function () {
    return Inertia::render('StudentDashboard');
})->name('student.dashboard');
