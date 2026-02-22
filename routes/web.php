<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Home page
Route::view('/', 'welcome')->name('home');

// Auth routes (من Breeze)
require __DIR__ . '/auth.php';


// Dashboard redirect based on role
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        return match($user->role->value) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            'user' => redirect()->route('home'), // ← User عادي يرجع للصفحة الرئيسية
            default => redirect()->route('home'),
        };
    })->name('dashboard');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    });

// Teacher routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])
    ->group(function () {
        Route::view('/dashboard', 'teacher.dashboard')->name('dashboard');
    });

// Student routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])
    ->group(function () {
        Route::view('/dashboard', 'student.dashboard')->name('dashboard');
    });

// Parent routes
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])
    ->group(function () {
        Route::view('/dashboard', 'parent.dashboard')->name('dashboard');
    });
