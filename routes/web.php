<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintController;

Route::redirect('/', 'admin')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/print/appointment-submission/{submission}', [PrintController::class, 'AppointmentSubmissionShow'])->name('print.appointment-submission');
    Route::get('/print/appointment-submission-content/{submission}', [PrintController::class, 'AppointmentSubmissionPrint'])->name('print.appointment-submission-content');
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