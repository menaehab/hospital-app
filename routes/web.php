<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentSubmissionController;

Route::redirect('/', 'admin')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/print/appointment-submission/{submission}', [AppointmentSubmissionController::class, 'show'])->name('appointment-submission.show');
    Route::get('/print/appointment-submission-content/{submission}', [AppointmentSubmissionController::class, 'print'])->name('appointment-submission.print');
});

require __DIR__.'/auth.php';


// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::redirect('settings', 'settings/profile');

//     Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
//     Volt::route('settings/password', 'settings.password')->name('settings.password');
//     Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
// });
