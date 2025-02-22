<?php

use App\Http\Controllers\PollController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\Admin;
use App\Models\Poll;
use Illuminate\Support\Facades\Route;

Route::get('/', [PollController::class, 'index'])->name('home');

Route::get('/dashboard', [PollController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/poll', [PollController::class, 'store'])->name('poll.store')->middleware(Admin::class);
    Route::delete('/poll/{id}', [PollController::class, 'destroy'])->name('poll.delete')->middleware(Admin::class);
});

Route::get('/poll/{uid}', [PollController::class, 'show'])->name('poll.show');
Route::post('/poll/vote', [PollController::class, 'vote'])->name('poll.vote');


require __DIR__ . '/auth.php';
