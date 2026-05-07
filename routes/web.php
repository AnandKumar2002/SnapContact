<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // --- Contact Management ---
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::view('/', 'pages.contacts.index')->name('index');
        Route::livewire('/create', 'pages::contacts.create')->name('create');
    });
});

require __DIR__.'/settings.php';
